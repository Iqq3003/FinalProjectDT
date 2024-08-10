<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('success' => false);

    if (isset($_FILES['user_img_file']) && $_FILES['user_img_file']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'user_img/';
        $uploadFile = $uploadDir . basename($_FILES['user_img_file']['name']);

        if (move_uploaded_file($_FILES['user_img_file']['tmp_name'], $uploadFile)) {
            $response['success'] = true;
            $response['filepath'] = $uploadFile;
        }
    }

    echo json_encode($response);
}
?>
