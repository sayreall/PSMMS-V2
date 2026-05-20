<?php

namespace App\Controllers;

use App\Config\App;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\PlanModel;

class PlansController extends BaseController
{
    /** @var PlanModel */
    private $planModel;

    public function __construct()
    {
        $this->planModel = new PlanModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $plans = $this->planModel->all(['id', 'product', 'plan', 'status', 'created_at'], [], ['id' => 'DESC']);

        return $this->render('plans.plan', [
            'title' => 'Plan',
            'plans' => $plans,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $product = trim($data['product'] ?? '');
        $plan = trim($data['plan'] ?? '');
        $status = trim($data['status'] ?? 'available');

        $rules = [
            'product' => 'required|max:100',
            'plan' => 'required|max:100',
            'status' => 'required|in:available,unavailable',
        ];

        $validator = (new Validation())->validate(compact('product', 'plan', 'status'), $rules);
        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add plan. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $this->planModel->createPlan([
            'product' => $product,
            'plan' => $plan,
            'status' => $status,
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'plan_create', "Created plan: {$product} {$plan}");

        Session::flash('message', 'Plan added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('plan'));
    }
}
