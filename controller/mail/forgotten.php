<?php
namespace Opencart\Admin\Controller\Mail;
/**
 * Class Forgotten
 *
 * @package Opencart\Admin\Controller\Mail
 */
class Forgotten extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * admin/model/user/user/editCode/after
	 *
	 * @param string            $route
	 * @param array<int, mixed> $args
	 * @param mixed             $output
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function index(string &$route, array &$args, &$output): void {
		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = '';
		}

		if (isset($args[0])) {
			$email = urldecode((string)$args[0]);
		} else {
			$email = '';
		}

		if (isset($args[1])) {
			$code = (string)$args[1];
		} else {
			$code = '';
		}

		if ($email && $code && ($route == 'common/forgotten.confirm') && filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->load->language('mail/forgotten');

			$company_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$subject = sprintf($this->language->get('text_subject'), $company_name);

			$data['text_greeting'] = sprintf($this->language->get('text_greeting'), $company_name);

			$data['reset'] = $this->url->link('common/forgotten.reset', 'email=' . $email . '&code=' . $code, true);
			$data['ip'] = oc_get_ip();

			$data['company'] = $company_name;
			$data['company_url'] = $this->config->get('config_company_url');

			if ($this->config->get('config_mail_engine')) {
				$mail_option = [
					'parameter'     => $this->config->get('config_mail_parameter'),
					'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
					'smtp_username' => $this->config->get('config_mail_smtp_username'),
					'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
					'smtp_port'     => $this->config->get('config_mail_smtp_port'),
					'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
				];

				$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
				$mail->setTo($email);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($company_name);
				$mail->setSubject($subject);
				$mail->setHtml($this->load->view('mail/forgotten', $data));
				$mail->send();
			}
		}
	}
}
