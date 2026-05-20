<?php

namespace App\Controllers;

use App\Config\App;
use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\AgentCodeModel;

class AgentCodeController extends BaseController
{
    private AgentCodeModel $agentCodeModel;

    public function __construct()
    {
        $this->agentCodeModel = new AgentCodeModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("\n            SELECT\n                ac.id,\n                sc.sales_category,\n                sa.agent_name,\n                ac.agent_code,\n                ac.status,\n                ac.validation\n            FROM agent_codes ac\n            LEFT JOIN sales_categories sc ON sc.id = ac.sales_category_id\n            LEFT JOIN sales_agents sa ON sa.id = ac.sales_agent_id\n            ORDER BY ac.id DESC\n        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $categoryStmt = $db->prepare("SELECT id, sales_category FROM sales_categories ORDER BY sales_category ASC");
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll(\PDO::FETCH_ASSOC);

        $agentStmt = $db->prepare("SELECT id, agent_name FROM sales_agents ORDER BY agent_name ASC");
        $agentStmt->execute();
        $agents = $agentStmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('sales.agent-code', [
            'title' => 'Agent Code',
            'rows' => $rows,
            'categories' => $categories,
            'agents' => $agents,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_category_id = (int)($data['sales_category_id'] ?? 0);
        $sales_agent_id = (int)($data['sales_agent_id'] ?? 0);
        $agent_code = trim($data['agent_code'] ?? '');

        $rules = [
            'sales_category_id' => 'required',
            'sales_agent_id' => 'required',
            'agent_code' => 'required|min:3|max:50',
        ];

        $validator = (new Validation())->validate(
            [
                'sales_category_id' => $sales_category_id,
                'sales_agent_id' => $sales_agent_id,
                'agent_code' => $agent_code,
            ],
            $rules
        );

        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add agent code. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $this->agentCodeModel->createAgentCode([
            'sales_category_id' => $sales_category_id,
            'sales_agent_id' => $sales_agent_id,
            'agent_code' => $agent_code,
            'status' => 'active',
            'validation' => 'pending',
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'agent_code_create', "Created agent code: {$agent_code}");

        Session::flash('message', 'Agent code added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('agent-code'));
    }
}
