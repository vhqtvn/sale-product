<?php
	
	App::import('Model', 'CronGather');
	App::import('Model', 'Amazonaccount');
	
	class CornShell extends AppShell {
		public function doACron() {
			$cronGather = new CronGather() ;
			$cronGather->amazonAsin("5","A") ;
			$cronGather->amazonShippingAsin("5","A") ;
			$cronGather->gatherAmazonFba("5","A") ;
			$cronGather->gatherAmazonCompetitions("5","A") ;
		}
		
		public function doBCron() {
			$cronGather = new CronGather() ;
			$cronGather->amazonAsin("5","B") ;
			$cronGather->amazonShippingAsin("5","B") ;
			$cronGather->gatherAmazonFba("5","B") ;
			$cronGather->gatherAmazonCompetitions("5","B") ;
		}
		
		public function doCCron() {
			$cronGather = new CronGather() ;
			$cronGather->amazonAsin("5","C") ;
			$cronGather->amazonShippingAsin("5","C") ;
			$cronGather->gatherAmazonFba("5","C") ;
			$cronGather->gatherAmazonCompetitions("5","C") ;
		}
		
		public function doDCron() {
			$cronGather = new CronGather() ;
			$cronGather->amazonAsin("5","D") ;
			$cronGather->amazonShippingAsin("5","D") ;
			$cronGather->gatherAmazonFba("5","D") ;
			$cronGather->gatherAmazonCompetitions("5","D") ;
		}
	}
