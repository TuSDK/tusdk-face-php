<?php

/*
 * TuSDK 人脸分组服务API调用示例
 */

require 'TuFace.php';

try {
    // 图片路径 或 图片URL
    $srcFile = 'https://tusdk.com/images/face/f-l2.jpg';

    // 用户分组名称/ID
    $groupId = 'my_group_id';

    // 用户ID
    $uid = 'my_uid_1';

    // 实例化TuFace
    $tuFace = new TuFace($srcFile);

    // 人脸分组保存
    $saveData = $tuFace->request('save', array('group_id' => $groupId, 'uid' => $uid));
    print_r($saveData);

    // 人脸分组查找
    $file2 = 'https://tusdk.com/images/face/f-r2.jpg';
    $tuFace->setFile($file2);
    $findData = $tuFace->request('find', array('group_id' => $groupId));
    print_r($findData);
    
} catch (Exception $e) {
    echo $e->getMessage();
}