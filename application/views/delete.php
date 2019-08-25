<?php

echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
    <li class="breadcrumb-item active" aria-current="page">'.$Title.'</li>
  </ol>
</nav>';

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-times-circle"></span> '.$Title.'</h1>';

echo $Content;

echo "<br /><br />";
echo '<div style="max-width: 400px; padding: 10px;" class="container">';
echo form_open('delete');
echo '<strong>'.$this->lang->line('a1170').'</strong><br /><br />';
echo form_error('acceptdelete','<br /><div class="alert alert-danger">', '</div>');
echo form_checkbox('acceptdelete', 'yes').''.$this->lang->line('a1171').'<br /><br />';
echo form_hidden('formdelete','yes');
echo form_submit(array('name' => 'buttonstart', 'value' => ''.$this->lang->line('a1172').'', 'class' => 'btn btn-danger btn-block'));
echo form_close();

echo '<div>';

echo '</div>';

?>