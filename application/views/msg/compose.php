<?php
/*
needs: form validation, upload of photo
*/


echo heading("Compose a Message!", 2);
echo form_open('messages/send_message');

echo form_label('To', 'to_id');
unset($usernames[$_SESSION['userid']]);
echo form_dropdown('to_id',$usernames);

echo form_label('Subject', 'subject');
$input = array('name' => 'subject', 'id' => 'subject', 'size'=> 40);
echo form_input($input);


echo form_label('Message', 'message');
$input = array('name' => 'message', 'id' => 'message', 'rows'=> 10, 'cols' => 40);
echo form_textarea($input);

echo br(2);
echo form_submit('send','send');

echo form_close();

?>