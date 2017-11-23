<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class provides a wrapper for Datatables call to the CI's DB library.
 * A typical Datatables call is a POST request with form data as defined below : 
 *  draw:3
 * columns[0][data]:table_column_name_or_an_integer
 * columns[0][name]:
 * columns[0][searchable]:true
 * columns[0][orderable]:true
 * columns[0][search][value]:
 * columns[0][search][regex]:false
 *
 * .. another columns settings
 * 
  order[0][column]:0
  order[0][dir]:asc
  start:0
  length:10
  search[value]:tea
  search[regex]:false
 * 
 * See https://datatables.net/manual/server-side for complete references of sent parameters
 *
 * @author Slurp
 */
class Datatables3 {

    /**
     * Global container variables for chained argument results
     *
     */
    private $ci;
    private $distinct;
    private $columns;
    private $select = [];
    private $search_columns = [];

    /**
     * Copies an instance of CI
     */
    public function __construct() {
        $this->ci = & get_instance();
    }

    /**
     * Initialize datatables to cache all database query builder commands
     */
    public function init() {
        $this->ci->db->start_cache();
        return $this;
    }

    //the final method to call
    public function generate() {
        // we need to make 3 db queries to get complete infomation to send into response
        // the first query is to compute recordsTotal
        // the second one is computing recordsFiltered, applying the search value(s)
        // and the third one to get the actual data rows

        $this->ci->db->stop_cache();
        // FIRST Query
        $this->ci->db->distinct($this->distinct);
        $recordsTotal = $this->ci->db->get()->num_rows();
        $firstQ = $this->ci->db->last_query();
        // SECOND Query, applying filter without paging
        $this->ci->db->start_cache();
        $this->_filter();
        $this->ci->db->stop_cache();
        $this->ci->db->distinct($this->distinct);
        $recordsFiltered = $this->ci->db->get()->num_rows();
        $secondQ = $this->ci->db->last_query();
        // THIRD Query, get the data rows
        $this->_page();
        $this->_order();
        $this->ci->db->distinct($this->distinct);
        $resultSet = $this->ci->db->get()->result_array();
        $thirdQ = $this->ci->db->last_query();
        $this->ci->db->flush_cache();

        /**
         * compile result based on whether we use indexed columns or named columns
         * in case of indexed columns (array based data source), the data array is tailored as follows
         * "data": [
          [
          "Airi",
          "Satou",
          "Accountant",
          "Tokyo",
          "28th Nov 08",
          "$162,700"
          ],
          [
          "Angelica",
          "Ramos",
          "Chief Executive Officer (CEO)",
          "London",
          "9th Oct 09",
          "$1,200,000"
          ],
         * ]
         * 
         * meanwhile if we use named columns, the data array will be tailored as follows
         * "data": [
          {
          "first_name": "Airi",
          "last_name": "Satou",
          "position": "Accountant",
          "office": "Tokyo",
          "start_date": "28th Nov 08",
          "salary": "$162,700"
          },
          {
          "first_name": "Angelica",
          "last_name": "Ramos",
          "position": "Chief Executive Officer (CEO)",
          "office": "London",
          "start_date": "9th Oct 09",
          "salary": "$1,200,000"
          },
         * ]
         */
        $aaData = [];
        foreach ($resultSet as $row_key => $row_val) {
            $aaData[$row_key] = $row_val;
            if ($this->isColIdxd()) {
                $aaData[$row_key] = array_values($aaData[$row_key]);
            }
        }
        return json_encode([
            'draw' => intval($this->ci->input->post('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $aaData,
            //'search_columns' => $this->search_columns,
            //'columns' => $this->columns,
            //'select' => $this->select,
            //'first'=>$firstQ,
            //'second'=>$secondQ,
            //'third'=>$thirdQ,
            $this->ci->security->get_csrf_token_name() => $this->ci->security->get_csrf_hash()
        ]);
    }

    /**
     * We take these parameters into consideration in filtering results :
     * - search[value]
     * - columns[i][searchable]
     * - columns[i][search][value]
     * 
     */
    private function _filter() {
        $sWhere = '';
        $mColArray = $this->ci->input->post('columns');
        $search = $this->ci->input->post('search');
        $sSearch = $this->ci->db->escape_like_str(trim($search['value']));
        if ($sSearch != '') {
            // custom field to search in
            for ($i = 0; $i < count($this->search_columns); $i++) {
                $sWhere .= 'UPPER(' . $this->search_columns[$i] . ") LIKE '%" . strtoupper($sSearch) . "%' OR ";
            }
            for ($i = 0; $i < count($mColArray); $i++) {
                if ($mColArray[$i]['searchable'] == 'true') {
                    if ($this->isColIdxd()) {
                        $sWhere .= 'UPPER(' . $this->select[$this->columns[$i]] . ") LIKE '%" . strtoupper($sSearch) . "%' OR ";
                    } else {
                        $sWhere .= 'UPPER(' . $this->select[$mColArray[$i]['data']] . ") LIKE '%" . strtoupper($sSearch) . "%' OR ";
                    }
                }
            }
        }
        $sWhere = substr_replace($sWhere, '', -3);
        if ($sWhere != '') {
            $this->ci->db->where('(' . $sWhere . ')');
        }
    }

    private function _page() {
        $iStart = $this->ci->input->post('start');
        $iLength = $this->ci->input->post('length');
        if ($iLength != '' && $iLength != '-1') {
            $this->ci->db->limit($iLength, ($iStart) ? $iStart : 0);
        }
    }

    /**
     * Check whether request come in with named column data
     */
    public function isColIdxd() {
        $column = $this->ci->input->post('columns');
        return is_numeric($column[0]['data']);
    }

    private function _order() {
        $columns = $this->ci->input->post('columns');
        if ($order = $this->ci->input->post('order')) {
            $colIdxd = $this->isColIdxd();
            foreach ($order as $key) {
                /**
                 * $o consists of 
                 * 
                 * $o[column] Column to which ordering should be applied. This
                 * is an index reference to the columns array of information 
                 * that is also submitted to the server.
                 * 
                 * $o[dir] Ordering direction for this column. It will be asc 
                 * or desc to indicate ascending ordering or descending ordering,
                 * respectively.
                 */
                $col = $colIdxd ?
                        $this->columns[$key['column']] :
                        $columns[$key['column']]['data'];
                $dir = $key['dir'];
                $this->ci->db->order_by($col, $dir);
            }
        }
    }

    public function add_search_column($arr) {
        $this->search_columns = array_merge($this->search_columns, $arr);
        return $this;
    }

    public function distinct($val = true) {
        $this->distinct = is_bool($val) ? $val : TRUE;
        return $this;
    }

    public function select($columns, $backtick_protect = TRUE) {
        foreach ($this->explode(',', $columns) as $val) {
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $column = preg_replace('/.*\.(.*)/i', '$1', $column); // get name after `.`
            $this->columns[] = $column;
            $this->select[$column] = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
        }
        $this->ci->db->select($columns, $backtick_protect);
        return $this;
    }

    /**
     * Return the difference of open and close characters
     *
     * @param string $str
     * @param string $open
     * @param string $close
     * @return string $retval
     */
    private function balanceChars($str, $open, $close) {
        $openCount = substr_count($str, $open);
        $closeCount = substr_count($str, $close);
        $retval = $openCount - $closeCount;
        return $retval;
    }

    private function explode($delimiter, $str, $open = '(', $close = ')') {
        $retval = array();
        $hold = array();
        $balance = 0;
        $parts = explode($delimiter, $str);
        foreach ($parts as $part) {
            $hold[] = $part;
            $balance += $this->balanceChars($part, $open, $close);
            if ($balance < 1) {
                $retval[] = implode($delimiter, $hold);
                $hold = array();
                $balance = 0;
            }
        }
        if (count($hold) > 0)
            $retval[] = implode($delimiter, $hold);
        return $retval;
    }

}
