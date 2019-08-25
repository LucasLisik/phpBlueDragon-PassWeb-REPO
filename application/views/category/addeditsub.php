<?php

if($action == 'add')
{
    $LabelName = ''.$this->lang->line('a1154').'';
}
else
{
    $LabelName = ''.$this->lang->line('a1155').'';
}

echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
    <li class="breadcrumb-item active" aria-current="page">'.$LabelName.'</li>
  </ol>
</nav>';

$ResultDB = $this->System_model->SelectOnlyMainCategoryList($_SESSION['user_id']);
        
$OptionsList = null;

foreach($ResultDB->result() as $row)
{
    $OptionsList[$row->category_id] = $this->System_model->decrypt($row->category_name,$_SESSION['user_iv'],$_SESSION['user_password']);
}

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-link"></span> '.$Title.'</h1>';

echo $Content.'<br /><br />';


if($new_category_added)
{
    echo '<div class="alert alert-success">'.$this->lang->line('a1156').'</div>';
}

if($category_edited)
{
    echo '<div class="alert alert-success">'.$this->lang->line('a1157').'</div>';
}

if($action == 'add')
{
    $ButtonLang = ''.$this->lang->line('a1154').'';
    echo form_open('addsubcategory');
}
else
{
    $ButtonLang = ''.$this->lang->line('a1155').'';
    echo form_open('editsubcategory/'.$CategoryId);
}

echo '<br /><strong>'.$this->lang->line('a1158').'</strong> <br /> '.form_dropdown('category_main', $OptionsList, $Vcategory_main, 'class="form-control"').'<br />';
echo form_error('category_main','<div class="alert alert-danger">', '</div>');
    
echo '<strong>'.$this->lang->line('a1159').'</strong> <br /> '.form_input(array('name' => 'category_name', 'id' => 'category_name', 'value' => $Vcategory_name, 'class' => 'form-control')).'<br />';
echo form_error('category_name','<div class="alert alert-danger">','</div>');

//echo form_hidden('project_id',$ProjectId);
echo form_hidden('formlogin','yes');
echo form_submit(array('name' => 'buttonstart', 'value' => $ButtonLang, 'class' => 'btn btn-info btn-block'));
echo form_close();

echo '</div>';

?>