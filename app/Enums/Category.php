<?php

namespace App\Enums;

enum Category: string {
    case General = 'general';
    case LegalLicensing = 'legal-licensing';
    case Financial = 'financial';
//    case FinancialDocs = 'financial-docs';
    case Operations = 'operations';
    case HumanResources = 'human-resources';
    case Customers = 'customers';
    case CompetitiveForces = 'competitive-forces';
    case Diagnostic = 'diagnostic';
    case DueDiligence = 'due-diligence';
    case Tax = 'tax';

    public static function labels(): array {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }

    public function label(): string {
        return match ($this) {
            self::General => 'General/Contextual',
            self::LegalLicensing => 'Legal, Compliance & Property',
            self::Financial => 'Financial Clarity & Reporting',
//            self::FinancialDocs => 'Financial Clarity & Reporting - DOCUMENTATION',
            self::Operations => 'Owner Dependency & Operations',
            self::HumanResources => 'People',
            self::Customers => 'Customer, Product & Revenue Quality',
            self::CompetitiveForces => 'Brand, IP & Intangibles',
            self::Diagnostic => 'Diagnostic',
            self::DueDiligence => 'Due Diligence & Risk Assessment',
            self::Tax => 'Tax Planning & Compliance',
        };
    }

    public static function default(): self {
        return self::General;
    }

    public static function valuesAsString(): string {
        return implode(',', self::values());
    }

    public static function values(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function colors(): array {
        return array_combine(
            self::values(),
            array_map(fn($case) => $case->color(), self::cases())
        );
    }

    public function color(): string {
        return match ($this) {
            self::General => '#007bff',
            self::LegalLicensing => '#dc3545',
            self::Financial => '#28a745',
//            self::FinancialDocs => '#6f42c1',
            self::Operations => '#fd7e14',
            self::HumanResources => '#17a2b8',
            self::Customers => '#ffc107',
            self::CompetitiveForces => '#6610f2',
            self::Diagnostic => '#00FF00', // Bright Green
            self::DueDiligence => '#20c997',  // Teal
            self::Tax => '#fd7e14',  // Orange
        };
    }

    public static function icons(): array {
        return array_combine(
            self::values(),
            array_map(fn($case) => $case->icon(), self::cases())
        );
    }

    public function icon(): string {
        return match ($this) {
            self::General => 'fa-solid fa-info-circle',
            self::LegalLicensing => 'fa-solid fa-gavel',
            self::Financial => 'fa-solid fa-chart-line',
//            self::FinancialDocs => 'fa-solid fa-file-invoice-dollar',
            self::Operations => 'fa-solid fa-cogs',
            self::HumanResources => 'fa-solid fa-users',
            self::Customers => 'fa-solid fa-handshake',
            self::CompetitiveForces => 'fa-solid fa-lightbulb',
            self::Diagnostic => 'fas fa-stethoscope',
            self::DueDiligence => 'fa-solid fa-search',
            self::Tax => 'fa-solid fa-file-invoice'
        };
    }

    public function initialCap(): string {
        return strtoupper(substr($this->label(), 0, 1));
    }
}
