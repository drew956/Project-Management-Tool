<?php

require_once "LH_Library.php";

$testImg = new HTMLElement();//HTMLElement should be an abstract class specific ones inherit from, so we don't have to do setSingle
    $testImg->setTag("img")->setSingle(true)->addAttribute("src='myImage.png'");
$baseElement = (new HTMLElement())->setTag("div")->addText("Hello")->addAttributes(array("class='main'", "id='myDiv'"));
$baseElement->addElement(  
   (new HTMLElement())->setTag("h1")->addText("Header!")->addAttribute("class='blueText'")
)->addElement(
   $testImg
);
$baseElement->printElement();

?>
