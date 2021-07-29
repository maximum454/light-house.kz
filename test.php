<?php
for ($ct = 0; $ct < count($_FILES['file']['tmp_name']); $ct++) {
    $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['file']['name'][$ct]));
    $filename = $_FILES['file']['name'][$ct];
    if (move_uploaded_file($_FILES['file']['tmp_name'][$ct], $uploadfile)) {
        $mail->addAttachment($uploadfile, $filename);
    } else {
        $msg .= 'Failed to move file to ' . $uploadfile;
    }
}
?>

<form id="quiz" class="form" enctype="multipart/form-data">
    <span class="input__label">Прикрепить фото или видео</span>
    <input class="input__field" type="file" multiple name="file" required>
    <button type='submit'>отправить</button>
</form>