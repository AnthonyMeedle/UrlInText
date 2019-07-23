<?php
namespace UrlInText\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Event\Loop\LoopExtendsParseResultsEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ProductQuery;
use Thelia\Model\FolderQuery;
use Thelia\Model\ContentQuery;

class UrlInTextListener implements EventSubscriberInterface
{
    /** @var Request */
    protected $request;

    public function __construct(Request $request){
        $this->request = $request;
    }
	
    public function parseResults(LoopExtendsParseResultsEvent $event)
    {
        $loopResult = $event->getLoopResult();

		foreach ($loopResult as $row) {
			$description = $row->get('DESCRIPTION');
			if (preg_match_all('|\[urlintext_([0-9])_([0-9]*)_url\]|',$description,$MesCategs, PREG_SET_ORDER)) {
				$Nb_Categ=count($MesCategs) ;
				foreach($MesCategs as $info){
					switch($info[1]){
						case 1 : $object = ProductQuery::create()->findPk($info[2]);  break;
						case 2 : $object = FolderQuery::create()->findPk($info[2]);  break;
						case 3 : $object = ContentQuery::create()->findPk($info[2]);  break;
						case 0 : default : $object = CategoryQuery::create()->findPk($info[2]);  break;
					}
					
					if(null !== $object){
						$object->setLocale($row->get('LOCALE'));
						$description = str_replace('[urlintext_'. $info[1] .'_'. $info[2] .'_url]', $object->getUrl() , $description);
						$description = str_replace('[urlintext_'. $info[1] .'_'. $info[2] .'_title]', $object->getTitle() , $description);
					}
				}
			}
			$row->set('DESCRIPTION', $description);
		}
    }


    public static function getSubscribedEvents()
    {
        return [
				TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'category') => ['parseResults', 128],
				TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'product') => ['parseResults', 128],
				TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'folder') => ['parseResults', 128],
				TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'content') => ['parseResults', 128],
				TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'associated_content') => ['parseResults', 128]
        ];
    }
}