<?php

if($CategoryIdIs == '')
{
    echo '<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
        <li class="breadcrumb-item active" aria-current="page">'.$this->lang->line('a1084').'</li>
      </ol>
    </nav>';
}
else
{
    //echo count($QueryResultTable);
    
    if(count($QueryResultTable) == 1)
    {
        //print_r($QueryResultTable);
        echo '<nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
                <li class="breadcrumb-item">'.$this->System_model->decrypt($QueryResultTable[0]['name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</li>
                <li class="breadcrumb-item active" aria-current="page">'.$this->lang->line('a1084').'</li>
            </ol>
        </nav>';
    }
    else
    {
        echo '<nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
                <li class="breadcrumb-item"><a href="'.base_url('index/'.$QueryResultTable[0]['id']).'">'.$this->System_model->decrypt($QueryResultTable[0]['name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>
                <li class="breadcrumb-item">'.$this->System_model->decrypt($QueryResultTable[1]['name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</li>
                <li class="breadcrumb-item active" aria-current="page">'.$this->lang->line('a1084').'</li>
            </ol>
        </nav>';
    }

}

?>
<script>
function copyToClipboard(element) 
{
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
  
  $("div#passwordWasDeleted").hide("fast");
  $("div#infocopied").show("fast").delay(3000).fadeOut();
}

function openNewWindow(element) 
{
  var ElementText = $(element).text();

  if(ElementText != "")
  {
        window.open(ElementText, "_blank", ""); 
  }
}

function openPasswordToView2(element)
{
    var UrlIs = "<?php echo base_url('getpswd/'); ?>" + element;
    
    //alert(UrlIs);
    
    $("#infopassword").load(UrlIs, function(responseTxt, statusTxt, xhr)
    {
        if(statusTxt == "success")
        {
            //alert("External content loaded successfully!");
            
            $("#infopassword").val(responseTxt);
            $("div#infopassword").show("fast").delay(10000).fadeOut(); 
            $("#infopassword").val(" ");
        }
        else if(statusTxt == "error")
        {
            alert("Error: " + xhr.status + ": " + xhr.statusText);
        }
    });
}

function openPasswordToView(element)
{
    var UrlIs = "<?php echo base_url('getpswd/'); ?>" + element;
    
    $.post(UrlIs, function( data ) 
    {
        $("#infopassword").html(data);
        $("#infopassword").show("fast").delay(10000).fadeOut();
    
    });
}
</script>
<?php

echo '<div id="resultPswd"></div>';

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-cogs"></span> '.$Title.'</h1>';

echo $Content.'<br /><br />';

echo $ContentPage;

echo '</div>';

?>