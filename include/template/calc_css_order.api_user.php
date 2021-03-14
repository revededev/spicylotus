<?php
$order_is_img = 4;
$order_isnot_img = 1;

if ($selected_user['path_certif']&& $selected_user['is_certif']!=='valide'){
    $order_certif = $order_is_img;
    $order_is_img--;
}else{
    $order_certif = $order_isnot_img;
    $order_isnot_img++;
}

if ($selected_user['path_assurance']&& $selected_user['is_assurance']!=='valide'){
    $order_assurance = $order_is_img;
    $order_is_img--;
}else{
    $order_assurance = $order_isnot_img;
    $order_isnot_img++;
}

if ($selected_user['path_licence']&& $selected_user['is_licence']!=='valide'){
    $order_licence = $order_is_img;
    $order_is_img--;
}else{
    $order_licence = $order_isnot_img;
    $order_isnot_img++;
}

if ($selected_user['path_yoga']&& $selected_user['is_yoga']!=='valide'){
    $order_yoga = $order_is_img;
    $order_is_img--;
}else{
    $order_yoga = $order_isnot_img;
    $order_isnot_img++;
}
?>