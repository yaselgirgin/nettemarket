<?php
namespace Opencart\Admin\Controller\Agenda;
/**
 * Class Calendar
 *
 * @package Opencart\Admin\Controller\Agenda
 */
class Calendar extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('agenda/calendar');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->document->addScript('view/javascript/jquery/fullcalendar/fullcalendar.min.js');
		$this->document->addScript('view/javascript/jquery/fullcalendar/rrule.min.js');
		$this->document->addScript('view/javascript/jquery/fullcalendar/fullcalendar.rule.min.js');
		$this->document->addScript('view/javascript/jquery/fullcalendar/fullcalendar.tr.js');
		$this->document->addScript('view/javascript/jquery/fullcalendar/fullcalendar.bootstrap5.js');
		$this->document->addScript('view/javascript/jquery/moment/moment.min.js');

		$data['config_calendar_view'] = $this->config->get('config_calendar_view');
		$data['open_hours'] = (array)$this->config->get('config_open_hours');
		$data['public_holidays'] = (array)$this->config->get('config_public_holidays');
		$data['religious_holiday_qurban'] = $this->config->get('config_religious_holiday_qurban');
		$data['religious_holiday_ramadan'] = $this->config->get('config_religious_holiday_ramadan');
		
		$islamic_year = substr($this->fromGregorian(date('Y') , 1, 1), 0, 4);
		$islamic_next = substr($this->fromGregorian(date('Y')+1 , 1, 1), 0, 4);
		$islamic_prev = substr($this->fromGregorian(date('Y')-1 , 1, 1), 0, 4);
		$qurban_prev = $this->toGregorian($islamic_prev , 12, 10, date('Y')-1);
		$qurban = $this->toGregorian($islamic_year , 12, 10, date('Y'));
		$qurban_next = $this->toGregorian($islamic_next , 12, 10, date('Y')+1);
		$ramadan_prev = $this->toGregorian($islamic_prev , 10, 01, date('Y')-1);		
		$ramadan = $this->toGregorian($islamic_year , 10, 01, date('Y'));
		$ramadan_next = $this->toGregorian($islamic_next , 10, 01, date('Y')+1);
				
		$data['qurban_prev'] = [
			'title'      		=> $this->language->get('religious_holiday_qurban'),
			'start_date'    => date('Y-m-d', strtotime($qurban_prev)),
			'end_date'   		=> date('Y-m-d', strtotime($qurban_prev. ' +4 days')),
		];

		$data['qurban'] = [
			'title'      		=> $this->language->get('religious_holiday_qurban'),
			'start_date'    => date('Y-m-d', strtotime($qurban)),
			'end_date'   		=> date('Y-m-d', strtotime($qurban. ' +4 days')),
		];
		
		$data['qurban_next'] = [
			'title'      		=> $this->language->get('religious_holiday_qurban'),
			'start_date'    => date('Y-m-d', strtotime($qurban_next)),
			'end_date'   		=> date('Y-m-d', strtotime($qurban_next. ' +4 days')),
		];

		$data['ramadan_prev'] = [
			'title'      		=> $this->language->get('religious_holiday_ramadan'),
			'start_date'    => date('Y-m-d', strtotime($ramadan_prev)),
			'end_date'   		=> date('Y-m-d', strtotime($ramadan_prev. ' +3 days')),
		];				

		$data['ramadan'] = [
			'title'      		=> $this->language->get('religious_holiday_ramadan'),
			'start_date'    => date('Y-m-d', strtotime($ramadan)),
			'end_date'   		=> date('Y-m-d', strtotime($ramadan. ' +3 days')),
		];
				
		$data['ramadan_next'] = [
			'title'      		=> $this->language->get('religious_holiday_ramadan'),
			'start_date'    => date('Y-m-d', strtotime($ramadan_next)),
			'end_date'   		=> date('Y-m-d', strtotime($ramadan_next. ' +3 days')),
		];

		$data['event'] = $this->load->controller('agenda/event.getForm');

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('agenda/calendar', $data));
	}

	public function	toGregorian($year, $month, $day, $gyear) {
			$y = $year;
			$m = $month;
			$d = $day;
			// http://www.oriold.uzh.ch/static/hegira.html
			$jd = floor((11 * $y + 3) / 30) + 354 * $y + 30 * $m - floor(($m - 1) / 2)+ $d + 1948440 - 386;
			if ($jd > 2299160 ) {
					$l = $jd + 68569;
					$n = floor((4 * $l) / 146097);
					$l = $l - floor((146097 * $n + 3) / 4);
					$i = floor((4000 * ($l + 1)) / 1461001);
					$l = $l-floor((1461 * $i) / 4) + 31;
					$j = floor((80 * $l) / 2447);
					$d = $l - floor((2447 * $j) / 80);
					$l = floor($j / 11);
					$m = $j + 2 - 12 * $l;
					$y = 100 * ($n - 49) + $i + $l;
			}  
			else {
					$j = $jd + 1402;
					$k = floor(($j - 1) / 1461);
					$l = $j - 1461 * $k;
					$n = floor(($l - 1) / 365) - floor($l / 1461);
					$i = $l - 365 * $n + 30;
					$j = floor((80 * $i) / 2447);
					$d = $i-floor((2447 * $j) / 80);
					$i = floor($j / 11);
					$m = $j + 2 - 12 * $i;
					$y = 4 * $k + $n + $i - 4716;
			}
			if ($y < $gyear) {
					return $this->toGregorian($year + 1, $month, $day - 1, $gyear);
			}
			return date($y.'-'.$m.'-'.$d);
	}

	public function fromGregorian($y, $m, $d) {
			// http://www.oriold.uzh.ch/static/hegira.html
			if (($y > 1582) || (($y == 1582) && ($m > 10)) || (($y == 1582) && ($m == 10) && ($d > 14))) {
					$jd = floor((1461 * ($y + 4800 + floor(($m - 14) / 12))) / 4) +
								floor((367 * ($m - 2 - 12 * (floor(($m - 14) / 12)))) / 12) -
								floor( (3 * (floor(($y + 4900 + floor(($m - 14) / 12)) / 100))) / 4) + $d - 32075;
			}
			else {
					$jd = 367 * $y - floor((7 * ($y + 5001 + floor(($m - 9) / 7))) / 4) + floor((275 * $m) / 9) + $d + 1729777;
			}
			$l = $jd - 1948440 + 10632;
			$n = floor(($l - 1) / 10631);
			$l = $l - 10631 * $n + 354;
			$j = (floor((10985 - $l) / 5316)) * (floor((50 * $l) / 17719)) + 
							(floor($l / 5670)) * (floor((43 * $l) / 15238));
			$l = $l - (floor((30 - $j) / 15)) * (floor((17719 * $j) / 50)) - 
							(floor($j / 16)) * (floor((15238 * $j) / 43)) + 29;
			$m = floor((24 * $l) / 709);
			$d = $l - floor((709 * $m) / 24);
			$y = 30 * $n + $j - 30;
			return date($y.'-'.$m.'-'.$d);
	}
}