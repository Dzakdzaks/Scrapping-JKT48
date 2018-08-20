<?php

require('simple_html_dom.php');

$base_url = "http://www.jkt48.com";

if(isset($_GET['id']))
    {
        $url_id = $_GET['id'];
        $url_referer_detail = $base_url . "/member/detail/id/" . $url_id; 
        $url_detail = $base_url . "/member/detail/id/" . $url_id . "/";

        $data_detail = getCurl($url_referer_detail, $url_detail);

        $output_detail['detail'] = array();

        $html_detail = str_get_html($data_detail);

        $get_list_detail = $html_detail->find('div[class=post]', 0);

        $get_name_detail = $get_list_detail->find('div[class=bioright]', 0)->plaintext;
        $get_birth_day = $get_list_detail->find('div[class=bioright]', 1)->plaintext;
        $get_blood_type = $get_list_detail->find('div[class=bioright]', 2)->plaintext;
        $get_horoskop = $get_list_detail->find('div[class=bioright]', 3)->plaintext;
        $get_height = $get_list_detail->find('div[class=bioright]', 4)->plaintext;
        $get_nick_name = $get_list_detail->find('div[class=bioright]', 5)->plaintext;
        $get_image_link = $get_list_detail->find('img', 0)->src;
        $get_video_link = $get_list_detail->find('iframe', 0)->src;
        $get_twitter_link = $get_list_detail->find('a[class=twitter-timeline]', 0);
        $get_instagram_link = $get_list_detail->find('div[id=instagramprofile]', 0);

        $temp = array();
        $temp['id'] = $url_id;
        $temp['surname'] = $get_name_detail;
        $temp['nickname'] = $get_nick_name;
        $temp['birthday'] = $get_birth_day;
        $temp['blood_type'] = $get_blood_type;
        $temp['horoskop'] = $get_horoskop;
        $temp['height'] = $get_height;
        $temp['image_link'] = $base_url . $get_image_link;
        $temp['video_link'] = $get_video_link;
        $temp['twitter_link'] = $get_twitter_link->getAttribute('href', 0);
        $temp['twitter_username'] = str_replace('Tweets by ', '', $get_twitter_link->plaintext);
        $temp['instagram_link'] = $get_instagram_link->find('a', 0)->href;
        $temp['instagram_username'] = $get_instagram_link->plaintext;
        array_push($output_detail['detail'], $temp);

        # wat ngubah ke json
        header ('Content-Type: application/json');
        echo json_encode($output_detail, JSON_PRETTY_PRINT);
    }   
else
    {
        $url_referer = $base_url . "/member/list";
        $url = $base_url ."/member/list/";

        $data = getCurl($url_referer, $url);

        $output = array();

        $TEAM_J_INDEX = 0;
        $TEAM_KIII_INDEX = 1;
        $TEAM_T_INDEX = 2;

        $html = str_get_html($data);

        $output['team_j'] = array();
        $output['team_kiii'] = array();
        $output['team_t'] = array();

        getMemberByTeam($html, $TEAM_J_INDEX, $output['team_j']);
        getMemberByTeam($html, $TEAM_KIII_INDEX, $output['team_kiii']);
        getMemberByTeam($html, $TEAM_T_INDEX, $output['team_t']);

        # wat ngubah ke json
        header ('Content-Type: application/json');
        echo json_encode($output, JSON_PRETTY_PRINT);
    }


function getMemberByTeam($html, $team_index, &$outputTeamMember) 
    {
        $get_list =$html->find('div[id=mainContent]', 0)->find('div[class=post]', $team_index)->find('div[class=profilepic]');

        foreach ($get_list as $key => $list)
        {
            $get_name = $list->find('a', 0)->find('img', 0);
            $get_id = $list->find('a', 0)->href;

            $get_id_validation = str_replace('/member/detail/id/', '', $get_id);
            $get_id_validation2 = str_replace('?lang=id', '', $get_id_validation);

            $temp['id'] = (int)$get_id_validation2;
            $temp['surname'] = $get_name->getAttribute('alt', 0);
            $temp['image'] = "http://www.jkt48.com" . $get_name->getAttribute('src', 0);
            array_push($outputTeamMember, $temp);
        }
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