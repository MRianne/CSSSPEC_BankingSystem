<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccountController extends BaseController {

	public function __construct() {
		parent::__construct();
	}

	public function create($id) {
		if (parent::is_user('admin') || parent::is_user('teller')) {

			$current_user = parent::current_user();

			$this->form_validation->set_rules('type_id', 'Account Type', 'trim|required');

			if ($this->form_validation->run()) {
				$account_id = $this->utilities->create_random_number(12);
				$this->account->insert([
					'account_id' => $account_id,
					'account_pin' => $this->encryption->encrypt('0000'),
					'customer_id' => $id,
					'type_id' => $this->input->post('type_id'),
					'balance' => 0.0,
					'status' => PENDING,
					'date_expiry' => NULL
				]);
				$this->approve_account($account_id);
				$this->session->set_flashdata('message', 'Account Successfully Created');
				return redirect('account/create/'. $id); // return to success #change this to view showing all account details
			}

			$data['error_message'] = validation_errors();
		    $data['error_message'] = explode("</p>", $data['error_message']);
		    $this->session->set_flashdata('error_message',  $data['error_message'][0]);

			return redirect('account/create/' . $id); // render create form w/ errors
		} else {
			return show_error("Forbidden Access", 403, "GET OUT OF HERE!!"); // return to page
		}
	}

	public function approve_account($id) {
		if (parent::is_user('admin') || parent::is_user('teller')) {
			$account = $this->account->get_protected($id);
			$current_user = parent::current_user();
			$initial_deposit = $account['account_type']['initial_deposit'];
			// $this->form_validation->set_rules('status', 'Account Status', 'trim|required');

			// if ($this->form_validation->run()) {
				$this->account->update($id, [
					'status' => $this->input->post('status') ?? 'ACTIVE',
					'balance' => $account['balance'] + $initial_deposit
				]);

				$updated_account = $this->account->get_protected($id);
				$this->transaction->insert([
					'transaction_id' => $this->utilities->create_random_string(),
					'account_id' => $id,
					'description' => INITIAL_DEPOSIT,
					'amount' => $initial_deposit,
					'type' => CREDIT,
					'balance' => $updated_account['balance'],
					'person_id' => $current_user->person_id,
					'date' => $updated_account['date_updated']
				]);
				return TRUE; // redirect to success
			// }

			// return FALSE; // render create form w/ errors
		} else {
			return FALSE; // return to page
		}
	}

	public function update_status($id) {
		if (parent::is_user('admin') || parent::is_user('teller')) {
			$account = $this->account->get_protected($id);
			$current_user = parent::current_user();

			$this->form_validation->set_rules('status', 'Account Status', 'trim|required');

			if ($this->form_validation->run()) {
				$this->account->update($id, [
					'status' => $this->input->post('status')
				]);
				return TRUE; // redirect to success
			}

			return FALSE; // render create form w/ errors
		} else {
			return FALSE; // return to page
		}
	}
	# to transfer in transaction?????
	// public function update_balance($id) {
	// 	if (parent::is_user('admin') || parent::is_user('teller') || parent::is_user('user')) {
	// 		$account = $this->account->get($id);
	// 		$current_user = parent::current_user();

	// 		$this->form_validation->set_rules('status', 'Account Status', 'trim|required');

	// 		if ($this->form_validation->run()) {
	// 			$this->account->update($id, [
	// 				'balance' => $this->input->post('balance')
	// 			]);
	// 			return TRUE; // redirect to success
	// 		}

	// 		return FALSE; // render create form w/ errors
	// 	} else {
	// 		return FALSE; // return to page
	// 	}
	// }

	public function viewBalance($id) {
		if(parent::is_user('user')) {
			$balance = $this->account->get_protected($id)['balance'];

			return parent::customerView('viewBalance', ['account_id' => $id, 'balance' => $balance]);
		} else {
			return show_error("Forbidden Access", 403, "GET OUT OF HERE!!"); // return to page
		}
	}

	public function balInq() {
		if(parent::is_user('user')) {
			$balance = $this->account->get_protected($this->input->post('account_id'))['balance'];

			return parent::customerView('viewBalance', ['account_id' => $this->input->post('account_id'), 'balance' => $balance]);
		} else {
			return show_error("Forbidden Access", 403, "GET OUT OF HERE!!"); // return to page
		}
	}

	public function balInqView() {
		if(parent::is_user('user')) {
			$accounts = $this->account->protected_get_many_by(['customer_id' => parent::current_user()->customer_id]);
			return parent::customerView('balInq', ['accounts' => $accounts]);
		} else {
			return show_error("Forbidden Access", 403, "GET OUT OF HERE!!"); // return to page
		}
	}

	public function get() {
		if(parent::is_user('admin') || parent::is_user('teller')) {

			$this->form_validation->set_rules('account_id', 'Account Number', 'trim|required|numeric|exact_length[12]');

			if($this->form_validation->run()) {

				$account = $this->account->get_protected($this->input->post('account_id'));
				$customer = $this->customer->with('person')->get($account['customer_id']);

				return parent::view('searchAccount', ['account' => $account, 'customer' => $customer]);
			}
			$data['error_message'] = validation_errors();
		    $this->session->set_flashdata('error_message',  $data['error_message']);
		    return redirect('account/search');
		} else {
			return show_error("Forbidden Access", 403, "GET OUT OF HERE!!"); // return to page
		}
	}

	public function get_all() {
		if(parent::is_user('admin') || parent::is_user('teller'))
			return $this->account->get_all_protected();
		return FALSE;
	}

	public function delete($id) {
		if(parent::is_user('admin'))
			return $this->account->delete($id);
		return FALSE;
	}
}
