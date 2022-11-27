

class TableFunctionButton {

	static createButton (buttonType, index, endpoint) {
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
		}
		if (button == null) {
			throw new Error("Could not create button, " + buttonType + " doesn't exist");
		}
		return button.createElement();
	}

}


class ButtonBase {

	constructor (index, endpoint, text, tableRowClass) {
		this.index = index;
		this.endpoint = endpoint;
		this.text = text;
		this.tableRowClass = tableRowClass;
	}

	createElement () {
		let buttonElement = jQuery("<a href='#'></a>");
		buttonElement.data("endpoint", this.endpoint);
		buttonElement.data("row-class", "table-row-" + this.index);
		buttonElement.text(this.text);
		if (!this.shouldShow) {
			buttonElement.css("display", "none");
		}
		buttonElement.addClass(this.classes.join(" "));
		return buttonElement;
	}

	get shouldShow () {
		return true;
	}

	get classes () {
		return [
			"table-operation",
			this.tableRowClass
		];
	}

}


class EditButton extends ButtonBase {

	constructor (index, endpoint) {
		super(index, endpoint, "Edit", "table-row-edit");
	}

}


class SaveButton extends ButtonBase {

	constructor (index, endpoint) {
		super(index, endpoint, "Save", "table-row-save");
	}

	get shouldShow () {
		return false;
	}

}


class CloseButton extends ButtonBase {

	constructor (index, endpoint) {
		super(index, endpoint, "Close", "table-row-close");
	}

	get shouldShow () {
		return false;
	}

}


class DeleteButton extends ButtonBase {

	constructor (index, endpoint) {
		super(index, endpoint, "Delete", "table-row-delete");
	}
}


class TableData {

	constructor (getDataEndpoint, updateDataEndpoint, deleteDataEndpoint) {
		this.getDataEndpoint = getDataEndpoint;
		this.updateDataEndpoint = updateDataEndpoint;
		this.deleteDataEndpoint = deleteDataEndpoint;
		this.cacheJsonResponse = null;
	}

	getRows (offset, limit) {
		let self = this;
		return fetch(
			"/wp-json/odoo-conn/v1/" + this.getDataEndpoint + "?" + new URLSearchParams(
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
		).then(function(response) {
			return response.json();
		}).then(function(jsonResponse) {
			self.cacheJsonResponse = jsonResponse;
		});
	}

}


class TableDisplay {

	constructor (tableData) {
		this.tableData = tableData;
		this.table = jQuery(".database-table");
		this.currentPageNumber = null;
		this.currentPage = null;
		this.showNext = false;
		this.numberOfRows = 10;
		this.displayData = null;
	}

	getUserFriendlyColumnNames () {
		throw new Error("NotImplementedError");
	}

	getDisplayColumns () {
		throw new Error("NotImplementedError");
	}

	getForeignKeys () {
		return [];
	}

	async getRows () {
		let pageNumber = this.currentPageNumber;
		if (!this.currentPageNumber || this.currentPageNumber < 0) {
			pageNumber = 0;
		}
		let offset = pageNumber * this.numberOfRows;

		await this.tableData.getRows(offset, this.numberOfRows + 1);

		this.displayData = this.tableData.cacheJsonResponse
		if (this.displayData.length == this.numberOfRows + 1) {
			this.showNext = true;
			this.displayData.pop();
		}
	}

	async displayTable () {
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

	refreshPageData () {
		let urlParams = new URLSearchParams(window.location.search);
		this.currentPageNumber = parseInt(urlParams.get("p")) || 0;
		this.currentPage = urlParams.get("page");
	}

	#addHeaderData () {
		let tHead = jQuery("<thead></thead>");
		let tableHeaderRow = jQuery("<tr></tr>");

		tableHeaderRow.append("<th>Edit</th>");
		let headers = this.getUserFriendlyColumnNames();
		headers.forEach( function (header) {
			let tableHeader = jQuery("<th>" + header + "</th>");
			tableHeaderRow.append(tableHeader);
		});
		tHead.append(tableHeaderRow);

		this.table.append(tHead);
	}

	#addTableData () {
		let dataRows = this.displayData;
		let self = this;
		let tBody = jQuery("<tbody></tbody>");
		dataRows.forEach( async function(dataRow, index) {
			let tableRow = jQuery("<tr></tr>");
			let tableData = jQuery("<td></td>");
			
			tableData.append(TableFunctionButton.createButton(
				"edit", index, self.tableData.updateDataEndpoint
			));
			tableData.append(TableFunctionButton.createButton(
				"save", index, self.tableData.updateDataEndpoint
			));
			tableData.append(TableFunctionButton.createButton(
				"close", index, self.tableData.updateDataEndpoint
			));
			let _delete = TableFunctionButton.createButton(
				"delete", index, self.tableData.deleteDataEndpoint
			);
			_delete.data("row-id", dataRow["id"]);
			tableData.append(_delete);

			tableRow.append(tableData);

			for ( let columnName in dataRow ) {
				if (!self.getDisplayColumns().includes(columnName)) {
					continue;
				}
				let editable = columnName == "id" ? false : true;
				
				let tableRowData = jQuery("<td></td>");
				let span = jQuery("<span>" + dataRow[columnName] + "</span>");
				span.addClass("table-row-" + index);
				span.data("editable", editable);

				if ( columnName in self.getForeignKeys() ) {
					let foreignKeyData = self.getForeignKeys()[columnName];

					span.data("foreign-key-endpoint", foreignKeyData["endpoint"]);
					span.data("table-field", foreignKeyData["keyColumn"]);
					span.data("foreign-key-column-primary-key", foreignKeyData["primaryKey"]);
					span.data("foreign-key-column-name", foreignKeyData["foreignColumnName"]);
					span.data("foreign-key-value", dataRow[foreignKeyData["keyColumn"]]);  // used to determin the current value of the drop down
				} else {
					span.data("table-field", columnName);
				}

				tableRowData.append(span);
				tableRow.append(tableRowData);
			}
			tBody.append(tableRow);
		});
		self.table.append(tBody);
	}

	#addNextButton () {
		if (jQuery("#next-button").length != 0) {
			// button is already on the screen
			return; 
		}

		let nextPageNumber = this.currentPageNumber + 1;
		let nextAnchor = jQuery(
			"<a id='next-button' href='?p=" + nextPageNumber + "&page=" + this.currentPage + "'>Next</a>"
		);
		jQuery("#pageination-display").append(nextAnchor);
	}

	#removeNextButton () {
		jQuery("#next-button").remove();
	}

	#addPreviousButton () {
		if (jQuery("#previous-button").length != 0) {
			// button is already on the screen
			return; 
		}

		let previousPageNumber = this.currentPageNumber - 1;
		let previousAnchor = jQuery(
			"<a id='previous-button' href='?p=" + previousPageNumber + "&page=" + this.currentPage + "'>Previous</a>"
		);
		jQuery("#pageination-display").append(previousAnchor);
	}

	#removePreviousButton () {
		jQuery("#previous-button").remove();
	}

}


class FormMappings extends TableDisplay {

	constructor () {
		let tableData = new TableData(
			"get-odoo-form-mappings", 
			"update-odoo-form-mapping", 
			"delete-odoo-form-mapping"
		);
		super(tableData);
	}

	getUserFriendlyColumnNames () {
		return [
			"Id", 
			"Odoo Form Id", 
			"Contact Form 7 Field Name", 
			"Odoo Field Name", 
			"Constant Value"
		];
	}

	getDisplayColumns () {
		return [
			"id",
			"odoo_form_name",
			"cf7_field_name",
			"odoo_field_name",
			"constant_value"
		];
	}

	getForeignKeys () {
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
	
	constructor () {
		let tableData = new TableData(
			"get-odoo-forms", 
			"update-odoo-form", 
			"delete-odoo-form"
		);
		super(tableData);
	}

	getUserFriendlyColumnNames () {
		return [
			"Id", 
			"Odoo Connection", 
			"Odoo Model", 
			"Name", 
			"Contact 7 Form"
		];
	}

	getDisplayColumns () {
		return [
			"id",
			"odoo_connection_name",
			"odoo_model",
			"name",
			"contact_7_title"
		];
	}

	getForeignKeys () {
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

	constructor () {
		let tableData = new TableData(
			"get-odoo-connections", 
			"update-odoo-connection", 
			"delete-odoo-connection"
		);
		super(tableData);
	}

	getUserFriendlyColumnNames () {
		return [
			"Id", 
			"Name", 
			"Username", 
			"URL", 
			"Database Name"
		];
	}

	getDisplayColumns () {
		return [
			"id",
			"name",
			"username",
			"url",
			"database_name"
		];
	}

}
