
class TableDisplay {

	constructor (getDataEndpoint, updateDataEndpoint) {
		this.getDataEndpoint = getDataEndpoint;
		this.updateDataEndpoint = updateDataEndpoint;
		this.table = jQuery(".database-table");
		this.cacheJsonResponse = null;
		this.currentPageNumber = null;
		this.currentPage = null;
		this.showNext = false;
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
		let size = 10;
		let pageNumber = this.currentPageNumber;
		if (!this.currentPageNumber || this.currentPageNumber < 0) {
			pageNumber = 0;
		}
		let start = pageNumber * size;
		let end = ((pageNumber + 1) * size) + 1;

		results = results.slice(start, end);

		if (results.length == size + 1) {
			this.showNext = true;
			results.pop();
		}

		return results;
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
		return ["Id", "Odoo Connection Id", "Odoo Model", "Name", "Contact 7 Form Id"];
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
