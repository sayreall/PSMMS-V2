<?php

namespace App\Models;

use App\Config\App;
use App\Config\Database;
use PDO;

class DashboardRouteResolver
{
    public function resolvePath(array $user): string
    {
        $role = strtolower(trim((string)($user['role'] ?? '')));
        $position = $this->resolvePosition($user);

        if ($position !== '') {
            if ($position === 'accounting') {
                return 'dashboard/accounting';
            }

            if ($position === 'super_manager') {
                return 'dashboard/asm-super-manager';
            }

            if ($position === 'area_sales_manager') {
                return 'dashboard/asm-area-sales-manager';
            }

            if ($position === 'head_manager') {
                return 'dashboard/head-manager';
            }

            if (in_array($position, ['general_manager', 'manager'], true)) {
                return 'dashboard/head-manager';
            }

            if (in_array($position, [
                'sales_admin',
                'admin',
                'dispatcher',
                'tech_leaders',
                'sales_team_leader',
                'validator',
                'backend',
                'marketing',
                'supervisor',
                'product_business_manager',
            ], true)) {
                return 'dashboard/admin-' . str_replace('_', '-', $position);
            }
        }

        if ($role === 'asm_manager') {
            return 'dashboard/asm-manager';
        }

        if ($role === 'super_admin') {
            return 'dashboard/super-admin';
        }

        if ($role === 'head_manager') {
            return 'dashboard/head-manager';
        }

        if ($role === 'accounting') {
            return 'dashboard/accounting';
        }

        if ($role === 'inhouse_sales') {
            return 'dashboard/inhouse-sales';
        }

        if ($role === 'msa_partners') {
            return 'dashboard/msa-partners';
        }

        if ($role === 'sme_sales') {
            return 'dashboard/sme-sales';
        }

        return 'dashboard/general';
    }

    public function resolveUrl(array $user): string
    {
        return App::url($this->resolvePath($user));
    }

    public function resolveDisplayRole(array $user): string
    {
        $position = $this->resolvePosition($user);
        if ($position !== '') {
            return ucwords(str_replace('_', ' ', $position));
        }

        $role = strtolower(trim((string)($user['role'] ?? 'user')));
        return ucwords(str_replace('_', ' ', $role !== '' ? $role : 'user'));
    }

    private function resolvePosition(array $user): string
    {
        $db = Database::getInstance();
        $userId = (int)($user['id'] ?? 0);

        if ($userId > 0) {
            $byUserId = $db->prepare("SELECT position FROM managers WHERE user_id = :user_id LIMIT 1");
            $byUserId->execute([':user_id' => $userId]);
            $row = $byUserId->fetch(PDO::FETCH_ASSOC) ?: [];
            $position = strtolower(trim((string)($row['position'] ?? '')));
            if ($position !== '') {
                return $position;
            }

            $byAdminUserId = $db->prepare("SELECT position FROM admins WHERE user_id = :user_id LIMIT 1");
            $byAdminUserId->execute([':user_id' => $userId]);
            $adminRow = $byAdminUserId->fetch(PDO::FETCH_ASSOC) ?: [];
            $adminPosition = strtolower(trim((string)($adminRow['position'] ?? '')));
            if ($adminPosition !== '') {
                return $adminPosition;
            }
        }

        $email = trim((string)($user['email'] ?? ''));
        $companyEmail = trim((string)($user['company_email'] ?? ''));
        if ($email === '' && $companyEmail === '') {
            return '';
        }

        $byEmail = $db->prepare("
            SELECT position
            FROM managers
            WHERE (:email_filter <> '' AND email = :email_value)
               OR (:company_email_filter <> '' AND company_email = :company_email_value)
            LIMIT 1
        ");
        $byEmail->execute([
            ':email_filter' => $email,
            ':email_value' => $email,
            ':company_email_filter' => $companyEmail,
            ':company_email_value' => $companyEmail,
        ]);

        $row = $byEmail->fetch(PDO::FETCH_ASSOC) ?: [];
        $managerPosition = strtolower(trim((string)($row['position'] ?? '')));
        if ($managerPosition !== '') {
            return $managerPosition;
        }

        $byAdminEmail = $db->prepare("
            SELECT position
            FROM admins
            WHERE (:email_filter <> '' AND email = :email_value)
               OR (:company_email_filter <> '' AND company_email = :company_email_value)
            LIMIT 1
        ");
        $byAdminEmail->execute([
            ':email_filter' => $email,
            ':email_value' => $email,
            ':company_email_filter' => $companyEmail,
            ':company_email_value' => $companyEmail,
        ]);

        $adminRow = $byAdminEmail->fetch(PDO::FETCH_ASSOC) ?: [];
        return strtolower(trim((string)($adminRow['position'] ?? '')));
    }
}
