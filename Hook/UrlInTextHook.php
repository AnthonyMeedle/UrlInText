<?php

namespace UrlInText\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;

class UrlInTextHook extends BaseHook {
    public function onObjectModificationFormRightBottom(HookRenderEvent $event){
		$html = $this->render("urlintextupdate.html");
		$event->add($html);		
    }
    public function onMainFooterJs(HookRenderEvent $event){
		$html = $this->render("scriptjs.html");
		$event->add($html);		
    }
}
?>