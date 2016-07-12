<?php

/*
 * TuSDK 人脸服务API调用示例
 */

require 'TuFace.php';

try {
    // 图片路径 或 图片URL
    $file = 'http://tusdk.com/images/face/f-l3.jpg';

    // 实例化TuFace
    $tuFace = new TuFace($file);

    // 人脸检测
    $faceData = $tuFace->request('detection');
    print_r($faceData);

    // 人脸标点
    $faceMarks = $tuFace->request('landmark', array('marks' => 5));
    print_r($faceMarks);

    // 人脸比对图片
    $file2 = 'http://tusdk.com/images/face/f-r3.jpg';
    $tuFace->setFile($file2);

    if ($faceData['ret'] == 200 && $faceData['data']) {
        // 人脸检测或标点返回faceId
        $faceId = $faceData['data']['items'][0]['faceId'];

        // api返回相似度
        $faceSimilarity = $tuFace->request('comparison', array('faceId' => $faceId));

        print_r($faceSimilarity);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}