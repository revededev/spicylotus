<?php
$the_class_img = "imgDocProfil";
if($suffix['item']==='monAvatar'){
    echo "<h3 class=''>Photo de profil </h3>";
    $the_class_img = "avatar";
}
$path = get_the_author_meta( "path_".$suffix['item'], $current_user->ID );
$status = get_the_author_meta( "is_".$suffix['item'], $current_user->ID );

?>
<form id='<?php echo $suffix["item"]; ?>' class='trois <?php echo $suffix["item"]; ?>' action='' method='post' enctype='multipart/form-data'>

<!-- <form id='<?php echo $suffix["item"]; ?>' class='trois <?php echo $suffix["item"]; ?>' action='<?php echo get_permalink().'#'.$suffix["item"]; ?>' method='post' enctype='multipart/form-data'> -->
    <p class="form-">
        <?php 
        if($path !==""){
            // Les éléments hidden sont envoyés sinon il faut les écrire en js si besoin ?>
            <img class="cible image <?php echo $the_class_img;?> imgOutPut" src='<?php echo $path; ?>' alt="<?php echo $suffix['alt'] ?>">
            <p   hidden class="cible noImage <?php echo $the_class_img;?> imgOutPut"><?php echo $suffix["noImage"]?></p>
        <?php
        }else{?>
            <img hidden class="cible image <?php echo $the_class_img;?> imgOutPut" src='<?php echo $path; ?>' alt="<?php echo $suffix['alt'] ?>">
            <p    class="cible noImage <?php echo $the_class_img;?> imgOutPut"><?php echo $suffix["noImage"]?></p>
        <?php
        }
        ?>
        <div class="supprime">
            <?php
            if($status!=="valide"){
                $hiddenSupp = $path==="" ? "hidden" : "";
                // echo " echo path : ".$path;
                // echo "<input type='submit' hidden name='submit' value='supprime_".$suffix["item"]."' />";
                echo "<input type='hidden' name='action' class='cible action-del' value='delete' size='25' />";
                echo "<p ".$hiddenSupp." class='cible supprimer supprime btn btn-secondary'>Supprimer</p>";
            }?>
        </div>
    </p> <!-- display image-->
    <?php
    if($suffix['item']!=='monAvatar'){
        echo "<p class='cible status ".$status."'>statut du document : ".$status."</p>";
    }

    if($suffix["item"]==="monAvatar"|| $status !=="valide"){
        if($suffix["item"]==="monAvatar"){
            $size = 6000000; // 6 Mo
            $accept = ".gif,.GIF,.jpeg,.JPEG,.jpg,.JPG";
        }else{
            $size = 10500000;
            $accept = ".pdf,.PDF,.jpeg,.JPEG,.jpg,.JPG";
        }
        ?>
        <p class="form-upload">
            <input type="submit" hidden name="submit" value="<?php echo $suffix['item'];?>" />
            <input type="hidden" name="action" class="cible action-upload" value="upload" size="25" />
            <input hidden type="number" class="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<?php echo $size;?>" /> <!-- = 2,00Mo-->
            <label class="form-upload" for="<?php echo $suffix['item'];?>"><?php echo $suffix['label'];?></label> 
            <input class="get_upload"type="file" id="<?php echo $suffix['item'];?>" name="<?php echo $suffix['item'];?>"
            accept="<?php echo $accept; ?>"  capture="environment"/>
            <?php
            // printr($_SESSION['error']);
            // if(isset($_FILES[$suffix['item']])&&!empty($_FILES[$suffix['item']])){
                if(isset($_SESSION['error'][$suffix['item']])&& !empty($_SESSION['error'][$suffix['item']])){
                    // echo '<li>'.$suffix['item'].'</li>';
                    echo "<ul>";
                    foreach($_SESSION['error'][$suffix['item']] as $element){
                        echo "<li style='color:red;'><i>".$element."</i></li>";
                    }
                    unset($_SESSION['error'][$suffix['item']]);
                    echo "</ul>";
                }elseif(isset($_SESSION['error'][$suffix['item']])){
                    // echo "<li style='color:red;'><i>Le fichier a été correctement téléchargé.</i></li>";
                        unset($_SESSION['error'][$suffix['item']]);
                }
            ?>
            <ul class='cible error erreur'></ul>
        </p><!-- input upload + msg errors -->
    <?php
    }
    ?>

<!-- </div> -->

<?php
echo "</form>";
?>