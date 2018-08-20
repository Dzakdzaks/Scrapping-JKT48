<?php 

require('simple_html_dom.php');

$base_url = "http://www.jkt48.com";

if (isset($_GET['id'])) {
	 	$url_id = $_GET['id'];
        $url_referer_detail = $base_url . "/news/detail/id/" . $url_id; 
        $url_detail = $base_url . "/news/detail/id/" . $url_id . "/";

        $data_detail = getCurl($url_referer_detail, $url_detail);

        $output_detail['detail_news'] = array();

        $html_detail = str_get_html($data_detail);

        $get_list_detail = $html_detail->find('div[class=post]', 0);

        $get_detail_title = $get_list_detail->find('h3', 0)->plaintext;
        $get_detail_date = $get_list_detail->find('div[class=metadata2]', 0)->plaintext;
        $get_detail_desc = $html_detail->find('div[class=post]', 0)->plaintext;      

        $get_desc_replace = str_replace($get_detail_title, "", $get_detail_desc);
        $get_desc_replace2 = str_replace($get_detail_date, "", $get_desc_replace);
        $get_desc_replace3 = str_replace("nbsp", "", $get_desc_replace2);

        $temp = array();
        $temp['detail_id'] = $url_id;
        $temp['detail_title'] = $get_detail_title;
        $temp['detail_date'] = $get_detail_date;
        $temp['detail_desc'] = trim(getAlphaNumericChars($get_desc_replace3));
        array_push($output_detail['detail_news'], $temp);

        # wat ngubah ke json
        header ('Content-Type: application/json');
        echo json_encode($output_detail, JSON_PRETTY_PRINT);
} else {
	$url_referer = $base_url . "/news/list";
	$url = $base_url ."/news/list";

	$output['news'] = array();

	$data = getCurl($url_referer, $url);

	$html = str_get_html($data);

	$get_list_news = $html->find('div[id=mainContent]', 0)->find('div[class=post]', 0)->find('div[class=contentpink]');

	foreach ($get_list_news as $key => $news) {
		$id = $news->find('div[class=excerpt]', 0)->find('a', 0)->href;
		$title = $news->find('div[class=excerpt]', 0)->find('a', 0)->plaintext;
		$type_news = $news->find('div[class=imgHolder]', 0)->find('img', 0)->src;
		$date = $news->find('div[class=metadata]', 0)->plaintext;

		$get_id_validation = str_replace('/news/detail/id/', '', $id);
        $get_id_validation2 = str_replace('?lang=id', '', $get_id_validation);

		$data = array();
		$data['id'] = $get_id_validation2;
		$data['type_news'] = $base_url . $type_news;
		$data['date'] = $date;
		$data['title'] = $title;
		array_push($output['news'], $data);
	}

	header ('Content-Type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT);
}




function getCurl($url_referer, $url)
    {
        # wat koneksi ke web tsb.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url_referer);
        curl_setopt($ch, CURLOPT_URL, $url);

        $site = curl_exec($ch);
        curl_close($ch);

        return $site;
    }

 function getAlphaNumericChars($text){
	$result = preg_replace("/[^a-zA-Z0-9\s]+/", "", $text);
	return $result;
}
