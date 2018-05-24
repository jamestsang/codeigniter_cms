<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_Listing {

    public $CI;
    public $list_setting;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->library("row");
    }

    public function initialize($setting = array()) {
        if (sizeOf($setting) == 0) {
            $this->list_setting = array(
                "ID"=>array(
                    "type" => "field",
                    "field" => "id",
                ),
                "Actions"=>array(
                    "type" => "action",
                    "actions" => array(
                        array(
                            "type" => "edit",
                            "url" => array(
                                "href" => "content/%s",
                                "params" => array("id")
                            )
                        ),
                        array(
                            "type" => "delete",
                            "url" => array(
                                "href" => "index/delete/%s",
                                "params" => array("id")
                            )
                        )
                    )
                )
            );
        } else {
            foreach ($setting as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function create_table($data) {
        $this->CI->row->initialize($this->list_setting, $this->main_id);
        $html = "";
        $head = "";
        $body = "";
        $head .= '<tr>';
        foreach ($this->list_setting as $key => $param) {
            $class = "";
            if(isset($param["mobile"]) && $param["mobile"] === false){
                $class = "hidden-xs";
            }
            $head .= '<td class="'.$class.'">';
            $head .= $this->sortField($key, $param);
            $head .= '</td>';
        }
        $head .= '</tr>';
        $ordering = array();
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $body .= '<tr id="' . $item[$this->main_id] . '">';
                $body .= $this->CI->row->getRow($item);
                $body .= '</tr>';
                $ordering[] = @$item["ordering"];
            }
        } else {
            $body .= '<tr><td class="empty-field" colspan="' . count($this->list_setting) . '">'.lang("No Data Found.").'</td></tr>';
        }

        $html .= '<table class="table table-striped">
					<thead>' . $head . '</thead>
					<tbody class="sortable">' . $body . '</tbody>
				 </table>';
        if ($this->canSort) {
            $html .= '
				<script>
					var ordering = ' . json_encode($ordering) . ';
				</script>
			';
        }
        return $html;
    }

    public function sortField($name, $param) {
        $name = langc(ucfirst(str_replace("_", " ", $name)));
        if (@$param["sort"] !== true && @$param["type"] != "ordering") {
            return $name;
        } else if (@$param["type"] == "ordering") {
            $is_order = $this->CI->input->get("ordering");
            $icon = "";
            if ($is_order) {
                $icon = '&nbsp;<span class="glyphicon glyphicon-resize-vertical"></span>';
            }
            $link = $this->CI->input->getFullPath(array("sort", "dir", "ordering")) . "&ordering=ordering";
            return '<a href="' . $link . '">' . $name . $icon . '</a>';
        } else {
            $dir = $this->CI->input->get("dir");
            $sort = $this->CI->input->get("sort");
            $icon = "";
            if ($sort == @$param["field"]) {
                if ($dir == "ASC") {
                    $dir = "DESC";
                    $icon = '&nbsp;<span class="glyphicon glyphicon-chevron-up"></span>';
                } else {
                    $dir = "ASC";
                    $icon = '&nbsp;<span class="glyphicon glyphicon-chevron-down"></span>';
                }
            } else {
                $dir = "ASC";
            }
            $link = $this->CI->input->getFullPath(array("sort", "dir", "ordering")) . "&sort=" . @$param["field"] . "&dir=" . $dir;

            return '<a href="' . $link . '">' . $name . $icon . '</a>';
        }
    }

}
