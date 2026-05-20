<?php

namespace App\Controllers;

use App\Config\App;
use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Models\ActivityLogModel;
use App\Models\SalesCategoryModel;

class SalesCategoryController extends BaseController
{
    private SalesCategoryModel $salesCategoryModel;

    public function __construct()
    {
        $this->salesCategoryModel = new SalesCategoryModel();

        if (!Auth::hasRole('super_admin')) {
            header('Location: ' . App::url('dashboard'));
            exit;
        }
    }

    public function index(): string
    {
        $rows = $this->salesCategoryModel->all(
            ['id', 'sales_category', 'sales_manager', 'type', 'tl_status', 'validation', 'created_at'],
            [],
            ['id' => 'DESC']
        );

        $salesManagers = [];
        $db = Database::getInstance();
        $managerStmt = $db->prepare("SELECT manager_name FROM managers ORDER BY manager_name ASC");
        $managerStmt->execute();
        $salesManagers = array_map(
            static fn(array $row): string => (string)($row['manager_name'] ?? ''),
            $managerStmt->fetchAll(\PDO::FETCH_ASSOC)
        );

        return $this->render('sales.category', [
            'title' => 'Sales Category',
            'rows' => $rows,
            'salesManagers' => $salesManagers,
        ]);
    }

    public function store(): void
    {
        Csrf::verify();

        $data = $this->requestData();
        $sales_category = trim($data['sales_category'] ?? '');
        $sales_manager = trim($data['sales_manager'] ?? '');
        $type = trim($data['type'] ?? '');

        $rules = [
            'sales_category' => 'required|min:2|max:150',
            'sales_manager' => 'required|min:2|max:150',
            'type' => 'required|in:partner,inhouse',
        ];

        $validator = (new Validation())->validate(compact('sales_category', 'sales_manager', 'type'), $rules);
        if (!$validator->passes()) {
            Session::flash('message', 'Unable to add sales category. Please check the form values.');
            Session::flash('message_type', 'error');
            $this->redirectBack();
        }

        $this->salesCategoryModel->createSalesCategory([
            'sales_category' => $sales_category,
            'sales_manager' => $sales_manager,
            'type' => $type,
            'tl_status' => 'active',
            'validation' => 'pending',
        ]);

        (new ActivityLogModel())->log(Auth::id(), 'sales_category_create', "Created sales category: {$sales_category}");

        Session::flash('message', 'Sales category added successfully.');
        Session::flash('message_type', 'success');
        $this->redirect(App::url('sales-category'));
    }
}
