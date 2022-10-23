
class TableDisplay {

	constructor (getDataEndpoint, updateDataEndpoint, deleteDataEndpoint) {
		this.getDataEndpoint = getDataEndpoint;
		this.updateDataEndpoint = updateDataEndpoint;
		this.deleteDataEndpoint = deleteDataEndpoint;
		this.table = jQuery(".database-table");
		this.cacheJsonResponse = null;
		this.currentPageNumber = null;
		this.currentPage = null;
		this.showNext = false;
		this.numberOfRows = 10;
	}

	getUserFriendlyColumnNames () {
		throw Error("NotImplementedError");
	}

	getDisplayColumns () {
		throw Error("NotImplementedError");
	}

	getForeignKeys () {
		return [];
	}

	getRows () {
		let pageNumber = this.currentPageNumber;
		if (!this.currentPageNumber || this.currentPageNumber < 0) {
			pageNumber = 0;
		}
		let offset = pageNumber * this.numberOfRows;

		let self = this;
		return fetch(
			"/wp-json/odoo-conn/v1/" + this.getDataEndpoint + "?" + new URLSearchParams(
				{
					offset: offset,
					limit: this.numberOfRows + 1
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
			if (jsonResponse.length == self.numberOfRows + 1) {
				self.showNext = true;
				jsonResponse.pop();
			}
			self.cacheJsonResponse = jsonResponse;
		});
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
		let dataRows = this.cacheJsonResponse;
		let self = this;
		let tBody = jQuery("<tbody></tbody>");
		dataRows.forEach( async function(dataRow, index) {
			let tableRow = jQuery("<tr></tr>");
			let tableData = jQuery("<td></td>");
			
			let edit = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.updateDataEndpoint + "'>Edit</a>"
			);
			edit.addClass("table-row-edit table-operation");
			tableData.append(edit);

			let save = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.updateDataEndpoint + "' style='display: none;'>Save</a>"
			);
			save.addClass("table-row-save table-operation");
			tableData.append(save);

			let close = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.updateDataEndpoint + "' style='display: none;'>Close</a>"
			);
			close.addClass("table-row-close table-operation");
			tableData.append(close);

			let delete_ = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.deleteDataEndpoint + "' data-row-id='" + dataRow["id"] + "'>Delete</a>"
			);
			delete_.addClass("table-row-delete table-operation");
			tableData.append(delete_);

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
		let nextAnchor = jQuery("<a id='next-button' href='?p=" + nextPageNumber + "&page=" + this.currentPage + "'>Next</a>");
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
		let previousAnchor = jQuery("<a id='previous-button' href='?p=" + previousPageNumber + "&page=" + this.currentPage + "'>Previous</a>");
		jQuery("#pageination-display").append(previousAnchor);
	}

	#removePreviousButton () {
		jQuery("#previous-button").remove();
	}

}


class FormMappings extends TableDisplay {

	constructor () {
		super("get-odoo-form-mappings", "update-odoo-form-mapping", "delete-odoo-form-mapping");
	}

	getUserFriendlyColumnNames () {
		return ["Id", "Odoo Form Id", "Contact Form 7 Field Name", "Odoo Field Name", "Constant Value"];
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
		super("get-odoo-forms", "update-odoo-form", "delete-odoo-form");
	}

	getUserFriendlyColumnNames () {
		return ["Id", "Odoo Connection", "Odoo Model", "Name", "Contact 7 Form"];
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
		super("get-odoo-connections", "update-odoo-connection", "delete-odoo-connection");
	}

	getUserFriendlyColumnNames () {
		return ["Id", "Name", "Username", "URL", "Database Name"];
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
