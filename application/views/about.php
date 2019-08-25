<?php

echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
    <li class="breadcrumb-item active" aria-current="page">'.$this->lang->line('a1090').'</li>
  </ol>
</nav>';

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-info"></span> '.$Title.'</h1>';
    
echo '<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">'.$Content.'</div>
    <div class="col-md-3"></div>
</div>';

echo '</div>';
?>