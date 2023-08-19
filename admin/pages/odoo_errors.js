class OdooErrors extends TableDisplay {

    constructor() {
        let tableData = new TableData(
            "get-odoo-errors",
            "",
            ""
        );
        super(tableData);
    }

    getUserFriendlyColumnNames() {
        return [
            "Id",
            "Contact 7 Form",
            "Time of Error",
            "Error Message"
        ];
    }

    getDisplayColumns() {
        return [
            "id",
            "contact_7_title",
            "time_occurred",
            "error_message"
        ];
    }

    getForeignKeys() {
        return {
            "contact_7_title": {
                "keyColumn": "contact_7_id",
                "endpoint": "get-contact-7-forms",
                "primaryKey": "ID",
                "foreignColumnName": "post_title"
            }
        }
    }

}

let tableDisplay = new OdooErrors();
tableDisplay.displayTable();
