<?php

namespace App\Controllers\Users\SmeSales;

use App\Config\App;
use App\Controllers\BaseController;
use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\Users\SmeSales\SalesAgentModel;

class SalesAgentController extends BaseController
{
    private $salesAgentModel;

    public function __construct()
    {
        $this->salesAgentModel = new SalesAgentModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("\n            SELECT\n                sa.id,\n                sc.sales_category,\n                sa.agent_name,\n                sa.status,\n                sa.validation\n            FROM sales_agents sa\n            LEFT JOIN sales_categories sc ON sc.id = sa.sales_category_id\n            ORDER BY sa.id DESC\n        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $categoryStmt = $db->prepare("SELECT id, sales_category FROM sales_categories ORDER BY sales_category ASC");
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('sme_sales.sales.agent', [
            'title' => 'Sales Agent',
            'rows' => $rows,
            'categories' => $categories,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_category_id = (int)($data['sales_category_id'] ?? 0);
        $agent_name = trim($data['agent_name'] ?? '');

        $rules = [
            'sales_category_id' => 'required',
            'agent_name' => 'required|min:2|max:150',
        ];

        $validator = (new Validation())->validate(
            ['sales_category_id' => $sales_category_id, 'agent_name' => $agent_name],
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add sales agent. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $this->salesAgentModel->createSalesAgent([
            'sales_category_id' => $sales_category_id,
            'agent_name' => $agent_name,
            'status' => 'active',
            'validation' => 'pending',
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'sales_agent_create', "Created sales agent: {$agent_name}");

        Session::flash('message', 'Sales agent added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('sales-agent'));
    }
}
