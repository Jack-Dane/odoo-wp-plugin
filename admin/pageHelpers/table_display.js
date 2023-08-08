class TableButtonBase {

    constructor(index, endpoint, text, tableRowClass) {
        this.index = index;
        this.endpoint = endpoint;
        this.text = text;
        this.tableRowClass = tableRowClass;
        this.buttonElement = null;
    }

    createElement() {
        let buttonElement = jQuery("<a href='#'></a>");
        buttonElement.data("endpoint", this.endpoint);
        buttonElement.data("row-class", "table-row-" + this.index);
        buttonElement.text(this.text);
        if (!this.shouldShow) {
            buttonElement.css("display", "none");
        }
        buttonElement.addClass(this.classes.join(" "));
        this.buttonElement = buttonElement;
        return buttonElement;
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

    click() { }

    static createButton(buttonType, index, endpoint) {
        let button = null;
        switch (buttonType) {
            case "edit":
                button = new EditButton(index, endpoint);
                break;
            case "save":
                button = new SaveButton(index, endpoint);
                break;
            case "close":
                button = new CloseButton(index, endpoint);
                break;
            case "delete":
                button = new DeleteButton(index, endpoint);
                break;
            case "test":
                button = new TestButton(index, endpoint);
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

    show () {
        this.buttonElement.show();
    }

}


class EditButton extends TableButtonBase {

    constructor(index, endpoint) {
        super(index, endpoint, "Edit", "table-row-edit");
    }

}


class SaveButton extends TableButtonBase {

    constructor(index, endpoint) {
        super(index, endpoint, "Save", "table-row-save");
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

            if (response.status != 200) {
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

    constructor(index, endpoint) {
        super(index, endpoint, "Close", "table-row-close");
    }

    get shouldShow() {
        return false;
    }

}


class DeleteButton extends TableButtonBase {

    constructor(index, endpoint) {
        super(index, endpoint, "Delete", "table-row-delete");
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
        ).then(function() {
            tableDisplay.displayTable();
        });
    }

}

class TestButton extends TableButtonBase {

    constructor(index, endpoint) {
        super(index, endpoint, "Test", "table-row-test");
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
        ).then(function (response){
            return response.json();
        });
    }

}


class RowField {

    constructor(index, columnName, text, editable) {
        this.index = index;
        this.columnName = columnName;
        this.text = text;
        this.editable = editable;
        this.dataElement = null;
    }

    createField() {
        this.dataElement = jQuery("<span>" + this.text + "</span>");
        this.dataElement.addClass("table-row-" + this.index);
        this.dataElement.data("editable", this.editable);
        this.dataElement.data("table-field", this.getTableField);
        return this.dataElement;
    }

    get getTableField () {
        return this.columnName;
    }

    closeField() {
        if (!this.editable) {
            return;
        }

        let tableField = this.dataElement.data("table-field");
        let span = jQuery("<span></span>");
        span.data("editable", this.editable);
        span.data("table-field", tableField);
        span.text(this.text);

        this.dataElement.replaceWith(span);
        this.dataElement = span;
    }

    async openField() {
        if (!this.editable) {
            return;
        }

        let input = jQuery("<input></input>");
        input.data("editable", this.editable);
        input.data("table-field", this.getTableField);
        input.addClass("table-row-" + this.index);
        input.attr("value", this.text);

        this.dataElement.replaceWith(input);
        this.dataElement = input;
    }

}


class DropDownRowField extends RowField {

    constructor(index, columnName, text, foreignKeyData, foreignKeyValue) {
        super(index, columnName, text, true);
        this.foreignKeyEndpoint = foreignKeyData["endpoint"];
        this.tableField = foreignKeyData["keyColumn"];
        this.foreignKeyColumnPrimaryKey = foreignKeyData["primaryKey"];
        this.foreignKeyColumnName = foreignKeyData["foreignColumnName"];
        this.foreignKeyValue = foreignKeyValue;
    }

    createField() {
        super.createField();
        this.dataElement.data("foreign-key-endpoint", this.foreignKeyEndpoint);
        this.dataElement.data("table-field", this.tableField);
        this.dataElement.data("foreign-key-column-primary-key", this.foreignKeyColumnPrimaryKey);
        this.dataElement.data("foreign-key-column-name", this.foreignKeyColumnName);
        this.dataElement.data("foreign-key-value", this.foreignKeyValue);
        return this.dataElement;
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

        let element = this.dataElement;
        let foreignKeyData = await this.#getForeignKeyData(
            element.data("foreign-key-endpoint")
        );

        let dropDown = jQuery("<select></select>");
        dropDown.data("editable", this.editable);
        dropDown.data("table-field", this.getTableField);
        dropDown.addClass("table-row-" + this.index);

        let selectedValue = element.data("foreign-key-value");
        foreignKeyData.forEach(function (foreignKeyObject) {
            let id = foreignKeyObject[element.data(
                "foreign-key-column-primary-key"
            )];
            let name = foreignKeyObject[element.data(
                "foreign-key-column-name"
            )];

            let option = jQuery("<option></option>");
            option.attr("value", id);
            option.text(name);

            if (selectedValue === id) {
                option.attr("selected", true);
            }
            dropDown.append(option);
        });
        element.replaceWith(dropDown);
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


class TableRow {

    constructor(index, id, tableData, dataRow, foreignKeys, displayColumns) {
        this.index = index;
        this.id = id;
        this.tableData = tableData

        this.editButton = null;
        this.saveButton = null;
        this.closeButton = null;
        this.deleteButton = null;

        this.rowFields = [];
        this.dataRow = dataRow;
        this.foreignKeys = foreignKeys;
        this.displayColumns = displayColumns;
    }

    createTableButtons() {
        this.editButton = TableButtonBase.createButton(
            "edit", this.index, this.tableData.updateDataEndpoint
        );
        this.editButton.buttonElement.on("click", this.editClick.bind(this));

        this.saveButton = TableButtonBase.createButton(
            "save", this.index, this.tableData.updateDataEndpoint
        );
        this.saveButton.buttonElement.on("click", this.saveClick.bind(this));

        this.closeButton = TableButtonBase.createButton(
            "close", this.index, this.tableData.updateDataEndpoint
        );
        this.closeButton.buttonElement.on("click", this.closeClick.bind(this));

        this.deleteButton = TableButtonBase.createButton(
            "delete", this.index, this.tableData.deleteDataEndpoint
        );
        this.deleteButton.buttonElement.data("row-id", this.id);
        this.deleteButton.buttonElement.on("click", this.deleteClick.bind(this));

        return [
            this.editButton.buttonElement,
            this.saveButton.buttonElement,
            this.closeButton.buttonElement,
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
                    this.index,
                    columnName,
                    fieldText,
                    this.foreignKeys[columnName],
                    this.dataRow[foreignKeyData["keyColumn"]]
                );
            } else {
                let editable = columnName!=="id";
                fieldObject = new RowField(
                    this.index,
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
        this.#closeFields();
        tableDisplay.displayTable();
    }

    deleteClick() {
        this.deleteButton.delete(this.id);
    }

    #closeFields() {
        this.rowFields.forEach(function(field) {
            field.closeField();
        });
    }

    #openFields() {
        this.rowFields.forEach(function(field) {
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


class TableData {

    constructor(getDataEndpoint, updateDataEndpoint, deleteDataEndpoint) {
        this.getDataEndpoint = getDataEndpoint;
        this.updateDataEndpoint = updateDataEndpoint;
        this.deleteDataEndpoint = deleteDataEndpoint;
        this.cacheJsonResponse = null;
    }

    createTableRowInstance(index, id, dataRow, foreignKeys, displayColumns) {
        return new TableRow(index, id, this, dataRow, foreignKeys, displayColumns);
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


class ConnectionTableData extends TableData {

    constructor(getDataEndpoint, updateDataEndpoint, deleteDataEndpoint, testDataEndpoint) {
        super(getDataEndpoint, updateDataEndpoint, deleteDataEndpoint);
        this.testDataEndpoint = testDataEndpoint;
    }

    createTableRowInstance(index, id, dataRow, foreignKeys, displayColumns) {
        return new ConnectionTableRow(index, id, this, dataRow, foreignKeys, displayColumns);
    }

}

class ConnectionTableRow extends TableRow {

    constructor(index, id, tableData, dataRow, foreignKeys, displayColumns) {
        super(index, id, tableData, dataRow, foreignKeys, displayColumns);

        this.testButton = null;
    }

    createTableButtons() {
        let tableButtons = super.createTableButtons();

        this.testButton = TableButtonBase.createButton(
            "test", this.index, this.tableData.testDataEndpoint
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
        return [];
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
        dataRows.forEach(async function (dataRow, index) {
            let tableRow = jQuery("<tr></tr>");
            let tableDataButtons = jQuery("<td></td>");

            let tableRowObject = self.tableData.createTableRowInstance(
                index, dataRow["id"], dataRow, self.getForeignKeys(), self.getDisplayColumns()
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
        if (jQuery("#next-button").length != 0) {
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
        if (jQuery("#previous-button").length != 0) {
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


class FormMappings extends TableDisplay {

    constructor() {
        let tableData = new TableData(
            "get-odoo-form-mappings",
            "update-odoo-form-mapping",
            "delete-odoo-form-mapping"
        );
        super(tableData);
    }

    getUserFriendlyColumnNames() {
        return [
            "Id",
            "Odoo Form Id",
            "Contact Form 7 Field Name",
            "Odoo Field Name",
            "Constant Value"
        ];
    }

    getDisplayColumns() {
        return [
            "id",
            "odoo_form_name",
            "cf7_field_name",
            "odoo_field_name",
            "constant_value"
        ];
    }

    getForeignKeys() {
        return {
            "odoo_form_name": {
                "keyColumn": "odoo_form_id",
                "endpoint": "get-odoo-forms",
                "primaryKey": "id",
                "foreignColumnName": "name"
            }
        }
    }

}


class OdooForms extends TableDisplay {

    constructor() {
        let tableData = new TableData(
            "get-odoo-forms",
            "update-odoo-form",
            "delete-odoo-form"
        );
        super(tableData);
    }

    getUserFriendlyColumnNames() {
        return [
            "Id",
            "Odoo Connection",
            "Odoo Model",
            "Name",
            "Contact 7 Form"
        ];
    }

    getDisplayColumns() {
        return [
            "id",
            "odoo_connection_name",
            "odoo_model",
            "name",
            "contact_7_title"
        ];
    }

    getForeignKeys() {
        return {
            "odoo_connection_name": {
                "keyColumn": "odoo_connection_id",
                "endpoint": "get-odoo-connections",
                "primaryKey": "id",
                "foreignColumnName": "name"
            },
            "contact_7_title": {
                "keyColumn": "contact_7_id",
                "endpoint": "get-contact-7-forms",
                "primaryKey": "ID",
                "foreignColumnName": "post_title"
            }
        }
    }

}


class OdooConnections extends TableDisplay {

    constructor() {
        let tableData = new ConnectionTableData(
            "get-odoo-connections",
            "update-odoo-connection",
            "delete-odoo-connection",
            "test-odoo-connection"
        );
        super(tableData);
    }

    getUserFriendlyColumnNames() {
        return [
            "Id",
            "Name",
            "Username",
            "URL",
            "Database Name"
        ];
    }

    getDisplayColumns() {
        return [
            "id",
            "name",
            "username",
            "url",
            "database_name"
        ];
    }

}
