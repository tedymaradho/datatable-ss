<?php
require_once '../koneksi.php';

if ($_GET['action'] == "table_data") {


    $columns = array(
        0 => 'product_id',
        1 => 'product_name',
        2 => 'price',
    );

    $querycount = $mysqli->query("SELECT count(product_id) as jumlah FROM tb_product");
    $datacount = $querycount->fetch_array();


    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $mysqli->query("SELECT product_id,product_name,price FROM tb_product order by $order $dir 
        																LIMIT $limit 
        																OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $mysqli->query("SELECT product_id,product_name,price FROM tb_product WHERE product_id LIKE '%$search%' 
            															or product_name LIKE '%$search%' 
            															order by $order $dir 
            															LIMIT $limit 
            															OFFSET $start");


        $querycount = $mysqli->query("SELECT count(product_id) as jumlah FROM tb_product WHERE product_id LIKE '%$search%' 
       																						or product_name LIKE '%$search%'");
        $datacount = $querycount->fetch_array();
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        while ($r = $query->fetch_array()) {
            $nestedData['no'] = $no;
            $nestedData['product_name'] = $r['product_name'];
            $nestedData['price'] = $r['price'];
            $nestedData['options'] = "<a href='#' class='btn-warning btn-sm'>Ubah</a>&nbsp; <a href='#' class='btn-danger btn-sm'>Hapus</a>";
            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = array(
        "draw" => intval($_POST['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    );

    echo json_encode($json_data);

}