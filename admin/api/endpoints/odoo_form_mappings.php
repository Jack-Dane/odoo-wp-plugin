<?php

namespace odoo_conn\admin\api\endpoints;


class FieldNameConstantValueException extends \Exception
{
}


trait OdooConnFormMappingTableName
{

    protected function get_table_name()
    {
        global $table_prefix;

        return $table_prefix . "odoo_conn_form_mapping";
    }

}


trait OdooConnFormMappingColumns
{

    protected function get_columns()
    {
        global $table_prefix;

        $columns = [
            $table_prefix . "odoo_conn_form_mapping.id",
            $table_prefix . "odoo_conn_form_mapping.odoo_form_id",
            $table_prefix . "odoo_conn_form.name as 'odoo_form_name'",
            $table_prefix . "odoo_conn_form_mapping.cf7_field_name",
            $table_prefix . "odoo_conn_form_mapping.odoo_field_name",
            $table_prefix . "odoo_conn_form_mapping.constant_value",
            $table_prefix . "odoo_conn_form_mapping.x_2_many"
        ];

        return implode(", ", $columns);
    }

}

class OdooConnGetOdooFormMappings extends OdooConnGetBaseSchema
{

    use OdooConnFormMappingTableName;
    use OdooConnFormMappingColumns;

    protected function foreign_keys()
    {
        global $table_prefix;

        return [
            "odoo_form_id" => [
                "table_name" => $table_prefix . "odoo_conn_form",
                "column_name" => "id"
            ]
        ];
    }

}


class OdooConnGetOdooFormMappingSingle extends OdooConnGetExtendedSchema
{

    use OdooConnFormMappingTableName;

    public function request($data)
    {
        $connections = parent::request($data);
        return !$connections ? null : $connections[0];
    }

    protected function where_query()
    {
        return "id=%d";
    }
}


class OdooConnPostOdooFormMappings extends OdooConnPostBaseSchema
{

    use OdooConnFormMappingTableName;

    public function request($data)
    {
        if ($this->is_field_set($data, "constant_value") && $this->is_field_set($data, "cf7_field_name")) {
            throw new FieldNameConstantValueException(
                "Can't pass both a constant value and a cf7 field name as arguments"
            );
        }

        return parent::request($data);
    }

    private function is_field_set($data, $field_name)
    {
        return isset($data[$field_name]) && $data[$field_name] != "";
    }

    protected function parse_data($data)
    {
        $x_2_many = ($data["x_2_many"] ?? "") === "on";
        return array(
            "odoo_form_id" => $data["odoo_form_id"],
            "cf7_field_name" => $data["cf7_field_name"],
            "odoo_field_name" => $data["odoo_field_name"],
            "constant_value" => $data["constant_value"],
            "x_2_many" => $x_2_many,
        );
    }

    protected function insert_data_types()
    {
        return array("%d", "%s", "%s", "%s", "%d");
    }

}


class OdooConnPutOdooFormMappings extends OdooConnPutBaseSchema
{

    use OdooConnFormMappingTableName;

    protected function update_data($data)
    {
        $parsed_data = [];

        if (!empty($data["constant_value"]) && !empty($data["cf7_field_name"])) {
            throw new FieldNameConstantValueException(
                "Can't pass both a constant value and a cf7 field name as arguments"
            );
        }

        return array_merge(
            $parsed_data,
            array(
                "constant_value" => $data["constant_value"],
                "cf7_field_name" => $data["cf7_field_name"],
                "odoo_form_id" => $data["odoo_form_id"],
                "odoo_field_name" => $data["odoo_field_name"],
            )
        );
    }
}

class OdooConnDeleteOdooFormMappings extends OdooConnDeleteBaseSchema
{

    use OdooConnFormMappingTableName;
}
