<?php


function check_num_ids ($ids)
{
    if (empty($ids) || !is_array($ids)) {
        return FALSE;
    }
    foreach ($ids as $id) {
        if (!is_numeric($id)) {
            return FALSE;
        }
    }
    return TRUE;
}

/*
 * 是数字返回 TRUE
 */
function check_num_id ($id)
{
    if (empty($id) || !is_numeric($id)) {
        return FALSE;
    }

    return TRUE;
}

function result_to_complex_map ($result, $field = 'id')
{
    $map = array();
    if (!$result || !is_array($result)) {
        return $map;
    }

    foreach ($result as $entry) {
        if (is_array($entry)) {
            if (isset($map[$entry[$field]])) {
                $map[$entry[$field]][] = $entry;
            } else {
                $map[$entry[$field]] = [$entry];
            }
        } else {
            if (isset($map[$entry->$field])) {
                $map[$entry->$field][] = $entry;
            } else {
                $map[$entry->$field] = [$entry];
            }
        }
    }
    return $map;
}

function result_to_map ($result, $field = 'id')
{
    $map = array();
    if (!$result || !is_array($result)) {
        return $map;
    }

    foreach ($result as $entry) {
        if (is_array($entry)) {
            $map[$entry[$field]] = $entry;
        } else {
            $map[$entry->$field] = $entry;
        }
    }
    return $map;
}

function result_to_array ($result, $field = 'id')
{
    $ary = array();
    if (!$result || !is_array($result)) {
        return $ary;
    }

    foreach ($result as $entry) {
        if (is_array($entry)) {
            $ary[] = $entry[$field];
        } elseif (is_object($entry)) {
            $ary[] = $entry->$field;
        }
    }
    return $ary;
}

/**
 * 发送post请求
 * @param string $url
 * @param string $param
 * @return bool|mixed
 */
function request_post ($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return FALSE;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch); //运行curl
    curl_close($ch);
    return $data;
}

/**
 * 发送get请求
 * @param string $url
 * @return bool|mixed
 */

function request_get ($url = '')
{
    if (empty($url)) {
        return FALSE;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 *  比较函数，适用于 sork usort uksork
 */
function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return $a > $b ? 1 : -1;
}