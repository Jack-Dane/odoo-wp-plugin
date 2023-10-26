class TableButtonBase {

    constructor(endpoint, text, tableRowClass) {
        this.endpoint = endpoint;
        this.text = text;
        this.tableRowClass = tableRowClass;
        this.buttonElement = null;
    }

    createElement() {
        this.buttonElement = jQuery("<a href='#'></a>");
        this.buttonElement.text(this.text);
        if (!this.shouldShow) {
            this.buttonElement.css("display", "none");
        }
        this.buttonElement.addClass(this.classes.join(" "));
        return this.buttonElement;
    }

    get shouldShow() {
        return true;
    }

    get classes() {
        return [
            "table-operation",
            this.tableRowClass
        ];
    }

    static createButton(buttonType, endpoint) {
        let button = null;
        switch (buttonType) {
            case "edit":
                button = new EditButton(endpoint);
                break;
            case "save":
                button = new SaveButton(endpoint);
                break;
            case "close":
                button = new CloseButton(endpoint);
                break;
            case "delete":
                button = new DeleteButton(endpoint);
                break;
            case "test":
                button = new TestButton(endpoint);
                break;
            default:
                throw new Error("Could not create button, " + buttonType + " doesn't exist");
        }
        button.createElement();
        return button;
    }

    hide() {
        this.buttonElement.hide();
    }

    show() {
        this.buttonElement.show();
    }

}


class EditButton extends TableButtonBase {

    constructor(endpoint) {
        super(endpoint, "Edit", "table-row-edit");
    }

}


class SaveButton extends TableButtonBase {

    constructor(endpoint) {
        super(endpoint, "Save", "table-row-save");
    }

    get shouldShow() {
        return false;
    }

    async save(updateData) {
        let joinParam = wpApiSettings.root.includes("?") ? "&" : "?";

        await fetch(
            wpApiSettings.root + "odoo_conn/v1/" + this.endpoint + joinParam + new URLSearchParams(
                updateData
            ),
            {
                method: "PUT",
                credentials: 'include',
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce
                }
            }
        ).then(function (response) {
            tableDisplay.displayTable();

            if (response.status !== 200) {
                let jsonPromise = Promise.resolve(response.json());

                jsonPromise.then(function (jsonResponse) {
                    // get the error message from the failed response
                    alert(jsonResponse["message"]);
                });
            }
        });
    }

}


class CloseButton extends TableButtonBase {

    constructor(endpoint) {
        super(endpoint, "Close", "table-row-close");
    }

    get shouldShow() {
        return false;
    }

}


class DeleteButton extends TableButtonBase {

    constructor(endpoint) {
        super(endpoint, "Delete", "table-row-delete");
    }

    async delete(id) {
        let joinParam = wpApiSettings.root.includes("?") ? "&" : "?";
        return fetch(
            wpApiSettings.root + "odoo_conn/v1/" + this.endpoint + joinParam + new URLSearchParams(
                {
                    id: id
                }
            ),
            {
                method: "DELETE",
                credentials: 'include',
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce
                }
            }
        ).then(function () {
            tableDisplay.displayTable();
        });
    }

}

class TestButton extends TableButtonBase {

    constructor(endpoint) {
        super(endpoint, "Test", "table-row-test");
    }

    async test(id) {
        let joinParam = wpApiSettings.root.includes("?") ? "&" : "?";
        return await fetch(
            wpApiSettings.root + "odoo_conn/v1/" + this.endpoint + joinParam + new URLSearchParams(
                {
                    id: id
                }
            ), {
                method: "GET",
                credentials: "include",
                headers: {
                    "content-type": "application/json",
                    "X-WP-Nonce": wpApiSettings.nonce
                }
            }
        ).then(function (response) {
            return response.json();
        });
    }

}


class RowField {

    constructor(columnName, text, editable) {
        this.columnName = columnName;
        this.text = text;
        this.editable = editable;
        this.dataElement = null;
    }

    createField() {
        this.dataElement = jQuery("<span>" + this.text + "</span>");
        return this.dataElement;
    }

    get getTableField() {
        return this.columnName;
    }

    closeField() {
        if (!this.editable) {
            return;
        }

        let span = jQuery("<span></span>");
        span.text(this.text);

        this.dataElement.replaceWith(span);
        this.dataElement = span;
    }

    async openField() {
        if (!this.editable) {
            return;
        }

        let input = jQuery("<input>");
        input.attr("value", this.text);

        this.dataElement.replaceWith(input);
        this.dataElement = input;
    }

}


class DropDownRowField extends RowField {

    constructor(columnName, text, foreignKeyData, foreignKeyValue) {
        super(columnName, text, true);
        this.foreignKeyEndpoint = foreignKeyData["endpoint"];
        this.tableField = foreignKeyData["keyColumn"];
        this.foreignKeyColumnPrimaryKey = foreignKeyData["primaryKey"];
        this.foreignKeyColumnName = foreignKeyData["foreignColumnName"];
        this.foreignKeyValue = foreignKeyValue;
    }

    get getTableField() {
        return this.tableField;
    }

    async #getForeignKeyData(foreignKeyData) {
        return await fetch(
            wpApiSettings.root + "odoo_conn/v1/" + foreignKeyData,
            {
                credentials: 'include',
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce
                }
            }
        ).then(function (response) {
            return response.json();
        });
    }

    async openField() {
        if (!this.editable) {
            return;
        }

        let foreignKeyData = await this.#getForeignKeyData(
            this.foreignKeyEndpoint
        );

        let dropDown = jQuery("<select></select>");

        let selectedValue = this.foreignKeyValue;
        foreignKeyData.forEach(function (foreignKeyObject) {
            let id = foreignKeyObject[this.foreignKeyColumnPrimaryKey];
            let name = foreignKeyObject[this.foreignKeyColumnName];

            let option = jQuery("<option></option>");
            option.attr("value", id);
            option.text(name);

            if (selectedValue === id) {
                option.attr("selected", true);
            }
            dropDown.append(option);
        }.bind(this));

        this.dataElement.replaceWith(dropDown);
        this.dataElement = dropDown;
    }

}


class PaginationButton {

    constructor(currentPageNumber, currentPage) {
        this.currentPageNumber = currentPageNumber;
        this.currentPage = currentPage;
    }

    createElement() {
        let element = jQuery("<a></a>");
        element.attr("id", this.idValue);
        element.attr("href", "?p=" + this.pageNumber + "&page=" + this.currentPage);
        element.text(this.text);
        return element;
    }

    static createButton(buttonType, pageNumber, currentPage) {
        let button = null;
        switch (buttonType) {
            case "next":
                button = new NextPaginationButton(pageNumber, currentPage);
                break;
            case "previous":
                button = new PerviousPageinationButton(pageNumber, currentPage);
                break;
        }
        if (button == null) {
            throw new Error("Could not create button, " + buttonType + " doesn't exist");
        }
        return button.createElement();
    }

}


class PerviousPageinationButton extends PaginationButton {

    get text() {
        return "Previous";
    }

    get pageNumber() {
        return this.currentPageNumber - 1;
    }

    get idValue() {
        return "previous-button";
    }

}


class NextPaginationButton extends PaginationButton {

    get text() {
        return "Next";
    }

    get pageNumber() {
        return this.currentPageNumber + 1;
    }

    get idValue() {
        return "next-button";
    }

}


class BaseTableRow {

    constructor(id, tableData, dataRow, foreignKeys, displayColumns) {
        this.id = id;
        this.tableData = tableData;
        this.dataRow = dataRow;
        this.foreignKeys = foreignKeys;
        this.displayColumns = displayColumns;
        this.rowFields = [];

        this.deleteButton = null;
    }

    createTableButtons () {
        this.deleteButton = TableButtonBase.createButton(
            "delete", this.tableData.deleteDataEndpoint
        );
        this.deleteButton.buttonElement.on("click", this.deleteClick.bind(this));

        return [
            this.deleteButton.buttonElement
        ];
    }

    createRowFields() {
        let fieldElements = [];
        for (let columnName in this.dataRow) {
            if (!this.displayColumns.includes(columnName)) {
                continue;
            }

            let fieldObject = null;
            let fieldText = this.dataRow[columnName];
            if (columnName in this.foreignKeys) {
                let foreignKeyData = this.foreignKeys[columnName];
                fieldObject = new DropDownRowField(
                    columnName,
                    fieldText,
                    this.foreignKeys[columnName],
                    this.dataRow[foreignKeyData["keyColumn"]]
                );
            } else {
                let editable = columnName !== "id";
                fieldObject = new RowField(
                    columnName,
                    fieldText,
                    editable
                );
            }

            this.rowFields.push(fieldObject);
            fieldElements.push(fieldObject.createField());
        }

        return fieldElements;
    }

    deleteClick() {
        this.deleteButton.delete(this.id);
    }

}


class TableRow extends BaseTableRow {

    constructor(id, tableData, dataRow, foreignKeys, displayColumns) {
        super(id, tableData, dataRow, foreignKeys, displayColumns);

        this.editButton = null;
        this.saveButton = null;
        this.closeButton = null;
    }

    createTableButtons() {
        let buttonElements = super.createTableButtons();

        this.editButton = TableButtonBase.createButton(
            "edit", this.tableData.updateDataEndpoint
        );
        this.editButton.buttonElement.on("click", this.editClick.bind(this));

        this.saveButton = TableButtonBase.createButton(
            "save", this.tableData.updateDataEndpoint
        );
        this.saveButton.buttonElement.on("click", this.saveClick.bind(this));

        this.closeButton = TableButtonBase.createButton(
            "close", this.tableData.updateDataEndpoint
        );
        this.closeButton.buttonElement.on("click", this.closeClick.bind(this));

        return buttonElements.concat([
            this.editButton.buttonElement,
            this.saveButton.buttonElement,
            this.closeButton.buttonElement
        ]);
    }

    editClick() {
        this.editButton.hide();
        this.saveButton.show();
        this.closeButton.show();
        this.deleteButton.hide();
        this.#openFields();
    }

    saveClick() {
        let updatedData = this.#getUpdatedData();
        this.saveButton.hide();
        this.closeButton.hide();
        this.editButton.show();
        this.saveButton.save(updatedData);
    }

    closeClick() {
        this.closeButton.hide();
        this.saveButton.hide();
        this.editButton.show();
        this.deleteButton.show();
        this.#closeFields();
    }

    #closeFields() {
        this.rowFields.forEach(function (field) {
            field.closeField();
        });
    }

    #openFields() {
        this.rowFields.forEach(function (field) {
            field.openField();
        });
    }

    #getUpdatedData() {
        let formData = {}
        this.rowFields.forEach(function (rowField) {
            let fieldName = rowField.getTableField;
            let fieldValue;
            if (rowField.editable) {
                fieldValue = rowField.dataElement.val();
            } else {
                fieldValue = rowField.dataElement.text();
            }
            formData[fieldName] = fieldValue;
        });
        return formData;
    }

}


class BaseTableData {

    constructor(getDataEndpoint, deleteDataEndpoint) {
        this.getDataEndpoint = getDataEndpoint;
        this.deleteDataEndpoint = deleteDataEndpoint;
        this.cacheJsonResponse = null;
    }

    getRows(offset, limit) {
        let self = this;
        let joinParam = wpApiSettings.root.includes("?") ? "&" : "?";
        return fetch(
            wpApiSettings.root + "odoo_conn/v1/" + this.getDataEndpoint + joinParam + new URLSearchParams(
                {
                    offset: offset,
                    limit: limit
                }
            ),
            {
                credentials: 'include',
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce
                }
            }
        ).then(function (response) {
            return response.json();
        }).then(function (jsonResponse) {
            self.cacheJsonResponse = jsonResponse;
        });
    }

}


class TableData extends BaseTableData {

    constructor(getDataEndpoint, updateDataEndpoint, deleteDataEndpoint) {
        super(getDataEndpoint, deleteDataEndpoint);
        this.updateDataEndpoint = updateDataEndpoint;
    }

    createTableRowInstance(id, dataRow, foreignKeys, displayColumns) {
        return new TableRow(id, this, dataRow, foreignKeys, displayColumns);
    }

}


class ConnectionTableData extends TableData {

    constructor(getDataEndpoint, updateDataEndpoint, deleteDataEndpoint, testDataEndpoint) {
        super(getDataEndpoint, updateDataEndpoint, deleteDataEndpoint);
        this.testDataEndpoint = testDataEndpoint;
    }

    createTableRowInstance(id, dataRow, foreignKeys, displayColumns) {
        return new ConnectionTableRow(id, this, dataRow, foreignKeys, displayColumns);
    }

}


class ConnectionTableRow extends TableRow {

    constructor(id, tableData, dataRow, foreignKeys, displayColumns) {
        super(id, tableData, dataRow, foreignKeys, displayColumns);

        this.testButton = null;
    }

    createTableButtons() {
        let tableButtons = super.createTableButtons();

        this.testButton = TableButtonBase.createButton(
            "test", this.tableData.testDataEndpoint
        );
        this.testButton.buttonElement.on("click", this.testClick.bind(this));
        tableButtons.push(this.testButton.buttonElement);

        return tableButtons;
    }

    async testClick() {
        let success = JSON.stringify(
            await this.testButton.test(this.id)
        );
        alert(success);
    }

    closeClick() {
        super.closeClick();
        this.testButton.show();
    }

    editClick() {
        super.editClick();
        this.testButton.hide();
    }

}


class OdooErrorTableData extends BaseTableData {

    constructor(getDataEndpoint, deleteDataEndpoint) {
        super(getDataEndpoint, deleteDataEndpoint);
    }

    createTableRowInstance(id, dataRow, foreignKeys, displayColumns) {
        return new BaseTableRow(id, this, dataRow, foreignKeys, displayColumns);
    }

}


class TableDisplay {

    constructor(tableData) {
        this.tableData = tableData;
        this.table = jQuery(".database-table");
        this.currentPageNumber = null;
        this.currentPage = null;
        this.showNext = false;
        this.numberOfRows = 10;
        this.displayData = null;
    }

    getUserFriendlyColumnNames() {
        throw new Error("NotImplementedError");
    }

    getDisplayColumns() {
        throw new Error("NotImplementedError");
    }

    getForeignKeys() {
        return {};
    }

    async getRows() {
        let pageNumber = this.currentPageNumber;
        if (!this.currentPageNumber || this.currentPageNumber < 0) {
            pageNumber = 0;
        }
        let offset = pageNumber * this.numberOfRows;

        await this.tableData.getRows(offset, this.numberOfRows + 1);

        this.displayData = this.tableData.cacheJsonResponse
        if (this.displayData.length === this.numberOfRows + 1) {
            this.showNext = true;
            this.displayData.pop();
        }
    }

    async displayTable() {
        this.refreshPageData();
        await this.getRows();

        this.table.empty();

        this.#addHeaderData();
        this.#addTableData();

        if (this.showNext) {
            this.#addNextButton();
        } else {
            this.#removeNextButton();
        }

        if (this.currentPageNumber > 0) {
            this.#addPreviousButton();
        } else {
            this.#removePreviousButton();
        }
    }

    refreshPageData() {
        let urlParams = new URLSearchParams(window.location.search);
        this.currentPageNumber = parseInt(urlParams.get("p")) || 0;
        this.currentPage = urlParams.get("page");
    }

    #addHeaderData() {
        let tHead = jQuery("<thead></thead>");
        let tableHeaderRow = jQuery("<tr></tr>");
        tableHeaderRow.addClass("wp-ui-highlight wp-core-ui");

        tableHeaderRow.append("<th>Edit</th>");
        let headers = this.getUserFriendlyColumnNames();
        headers.forEach(function (header) {
            let tableHeader = jQuery("<th>" + header + "</th>");
            tableHeaderRow.append(tableHeader);
        });
        tHead.append(tableHeaderRow);

        this.table.append(tHead);
    }

    #addTableData() {
        let dataRows = this.displayData;
        let self = this;
        let tBody = jQuery("<tbody></tbody>");
        dataRows.forEach(async function (dataRow) {
            let tableRow = jQuery("<tr></tr>");
            let tableDataButtons = jQuery("<td></td>");

            let tableRowObject = self.tableData.createTableRowInstance(
                dataRow["id"], dataRow, self.getForeignKeys(), self.getDisplayColumns()
            );
            let tableButtons = tableRowObject.createTableButtons();
            let tableFields = tableRowObject.createRowFields();

            tableButtons.forEach(function (tableButton) {
                tableDataButtons.append(tableButton);
            });
            tableRow.append(tableDataButtons);

            tableFields.forEach(function (tableField) {
                let tableDataField = jQuery("<td></td>");
                tableDataField.append(tableField);
                tableRow.append(tableDataField);
            });

            tBody.append(tableRow);
        });
        self.table.append(tBody);
    }

    #addNextButton() {
        if (jQuery("#next-button").length !== 0) {
            // button is already on the screen
            return;
        }
        jQuery("#pageination-display").append(PaginationButton.createButton(
            "next", this.currentPageNumber, this.currentPage
        ));
    }

    #removeNextButton() {
        jQuery("#next-button").remove();
    }

    #addPreviousButton() {
        if (jQuery("#previous-button").length !== 0) {
            // button is already on the screen
            return;
        }
        jQuery("#pageination-display").append(PaginationButton.createButton(
            "previous", this.currentPageNumber, this.currentPage
        ));
    }

    #removePreviousButton() {
        jQuery("#previous-button").remove();
    }

}
