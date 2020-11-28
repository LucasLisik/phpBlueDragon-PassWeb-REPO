<!doctype html>
<html lang="">
<head>
    <?php
    
    $UrlOfPage = base_url();
    
    /*$ResultDB = $this->System_model->GetSystemConfig();
    
    foreach($ResultDB->result() as $row)
    {
        $ConfigTable[$row->config_name] = $row->config_value;
    }*/
    
        echo '<title>'.$this->lang->line('a0937').'</title>';
    ?>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <script src="<?php echo $UrlOfPage; ?>library/popper.min.js"></script>
   <link rel="stylesheet" href="<?php echo $UrlOfPage; ?>library/stylesmenu.css" />
   <script src="<?php echo $UrlOfPage; ?>library/jquery.min.js" type="text/javascript"></script>
   <link rel="stylesheet" href="<?php echo $UrlOfPage; ?>library/jquery-ui/jquery-ui.theme.min.css" />
   <script src="<?php echo $UrlOfPage; ?>library/jquery-ui/jquery-ui.js"></script>
   <link href="<?php echo $UrlOfPage; ?>library/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
   <script src="<?php echo $UrlOfPage; ?>library/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
   
   <link rel="stylesheet" href="<?php echo $UrlOfPage; ?>library/font-awesome/css/font-awesome.min.css" />
   
   <link rel="shortcut icon" href="<?php echo $UrlOfPage; ?>favicon.ico" />
   
   <style>
	html, body
	{
		/*font-family: 'Open Sans', sans-serif;*/
		padding: 0px;
		margin: 0px;
	}

	.container
	{
		max-width: 1500px;
		margin-left: auto;
		margin-right: auto;
	}

	.header
	{
		width: 200px;
		float: left;
        font-family: 'Open Sans', sans-serif;
	}

	.menu
	{
		max-width: 500px;
		float: right;
	}

	@media (max-width: 599px)
	{
		.menu
		{
			float: none;
			clear: both;
		}
	}

	h1
	{
		color: #0677A0;
		padding: 0px;
		padding-left: 0px;
		font-weight: normal;
        font-family: 'Open Sans', sans-serif;
	}

	img 
	{
		max-width: 100%;
		height: auto;
	}
    
    div.DownloadRow
    {
        border: solid 1px #DDDDDD;
        border-radius: 5px;
        border-left: solid 5px #1B809E;
        border-right: solid 5px #1B809E;
        padding: 10px;
        text-align: center;
    }
    
    .btn-file {
        position: relative;
        overflow: hidden;
    }
    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
    
    .RowColor1
    {
        background-color: #F9F9F9;
        padding: 5px;
    }
    
    .RowColor2
    {
        background-color: #ffffff;
        padding: 5px;
    }
    
    .RowColor3
    {
        background-color: #ffffff;
        padding: 5px;
        font-weight: bold;
    }
    
    a, a:hover, h1, h2, h3, h4
    {
        color: #2BABD2;
    }
    
    .box
    {
    width: 200px;
    height:200px;
    margin:5px auto;
    text-align: center;
    line-height: 200px;
    }
    
    #boxusername
    {
        background-color: #d2d2d2;
    }
    
    #boxpassword
    {
        background-color: #000;
        color: #fff;
    }
    
    .bodytext2
    {
        padding-left: 5px;
        padding-right: 5px;
    }
	</style>
    <script language="JavaScript">
    function DeteleInfo(URL,Comunicate)
    {
    	if(confirm(Comunicate))
    	{
    		window.location = URL;
    	}
    }
    </script>
</head>
<body>
<div class="container" style="margin-top: 5px;">
     
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="d-flex flex-grow-1">
        <span class="w-100 d-lg-none d-block"></span>

            <a href="<?php echo $UrlOfPage; ?>" title="phpBlueDragon Pass Web" class="navbar-brand d-none d-lg-inline-block" style="color: #0677A0;"><img src="<?php echo $UrlOfPage; ?>library/logo.png" width="35" height="35" /> phpBlueDragon PassWeb</a>
            
        <a class="navbar-brand-two mx-auto d-lg-none d-inline-block" href="#"><img src="<?php echo $UrlOfPage; ?>library/logo.png" width="35" height="35" /></a>
        
        <div class="w-100 text-right">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
    </div>
    <div class="collapse navbar-collapse flex-grow-1 text-right" id="myNavbar">
        <ul class="navbar-nav ml-auto flex-nowrap">
            <?php
            if($_SESSION['user_id'] == "")
            {
                ?>
                <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>"><span class="fa fa-cogs" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1100'); ?></a>
                </li>
                <?php
            }
            else
            {
            ?>
            <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>"><span class="fa fa-cogs" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1101'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>main/"><span class="fa fa-cog" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1102'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>delete/"><span class="fa fa-times-circle" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1103'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>generator/"><span class="fa fa-hourglass-half" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1104'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>about/"><span class="fa fa-info" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1105'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link m-2 menu-item" style="color: #0677A0;" href="<?php echo $UrlOfPage; ?>logout/"><span class="fa fa-sign-out" style="color: #0677A0;"></span> <?php echo $this->lang->line('a1106'); ?></a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>
</nav>
        
</div>
<div style="clear: both;"></div>
    <div class="container">
    
        <?php
        /*
        $DontShow = true;
        
        if(!$DontShow)
        {
            ?><div class="alert alert-warning"><?php echo $this->lang->line('a1072'); ?></div><?php
        }
        */
        ?>