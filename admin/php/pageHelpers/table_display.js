
class TableDisplay {

	constructor (getDataEndpoint, updateDataEndpoint) {
		this.getDataEndpoint = getDataEndpoint;
		this.updateDataEndpoint = updateDataEndpoint;
		this.table = jQuery(".database-table");
		this.cacheJsonResponse = null;
	}

	getUserFriendlyColumnNames () {
		throw Error("NotImplementedError");
	}

	getRows () {
		let self = this;
		return fetch(
			"/wp-json/odoo-conn/v1/" + this.getDataEndpoint
		).then(function(response) {
			return response.json();
		}).then(function(jsonResponse) {
			self.cacheJsonResponse = jsonResponse;
		});
	}

	filterResults (results) {
		let urlParams = new URLSearchParams(window.location.search);
		let size = 10;
		let pageNumber = parseInt(urlParams.get("p"));
		if (!pageNumber || pageNumber < 0) {
			pageNumber = 0;
		}
		let start = pageNumber * size;
		let end = (pageNumber + 1) * size;

		results = results.slice(start, end);
		return results;
	}

	async displayTable () {
		await this.getRows();
		
		this.table.empty();

		this.#addHeaderData();
		this.#addTableData();
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
		dataRows = self.filterResults(dataRows);
		dataRows.forEach( function(dataRow, index) {
			let tableRow = jQuery("<tr></tr>");
			
			let edit = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.updateDataEndpoint + "'>Edit</a>"
			);
			edit.addClass("table-row-edit");
			tableRow.append(edit);

			let save = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.updateDataEndpoint + "' style='display: none;'>Save</a>"
			);
			save.addClass("table-row-save");
			tableRow.append(save);

			let close = jQuery(
				"<a href='#' data-row-class='table-row-" + index + "' data-endpoint='" + self.updateDataEndpoint + "' style='display: none;'>Close</a>"
			);
			close.addClass("table-row-close");
			tableRow.append(close);

			for ( let columnName in dataRow ) {
				let editable = true;
				if (columnName == "id") {
					editable = false;
				}

				let tableRowData = jQuery(
					"<td><span class='table-row-" + index + "' data-editable='" + editable + "' data-table-field='" + columnName + "'>" + dataRow[columnName] + "</span></td>"
				);
				tableRow.append(tableRowData);
			}
			tBody.append(tableRow);
		});
		self.table.append(tBody);
	}

}


class FormMappings extends TableDisplay {

	constructor () {
		super("get-odoo-form-mappings", "update-odoo-form-mapping");
	}

	getUserFriendlyColumnNames () {
		return ["Id", "Odoo Form Id", "Contact Form 7 Field Name", "Odoo Field Name", "Constant Value"];
	}

}


class OdooForms extends TableDisplay {
	
	constructor () {
		super("get-odoo-forms", "update-odoo-form");
	}

	getUserFriendlyColumnNames () {
		return ["Id", "Odoo Connection Id", "Name", "Contact Form 7 Id"];
	}

}


class OdooConnections extends TableDisplay {

	constructor () {
		super("get-odoo-connections", "update-odoo-connection");
	}

	getUserFriendlyColumnNames () {
		return ["Id", "Name", "Username", "API Key", "URL", "Database Name"];
	}

}
