import DashboardLayout from "@/pages/Dashboard/Layout/DashboardLayout.vue";
import AuthLayout from "@/pages/Dashboard/Pages/AuthLayout.vue";

// Dashboard pages
import Dashboard from "@/pages/Dashboard/Dashboard.vue";

// Profile
import UserProfile from "@/pages/User/UserProfile/UserProfile.vue";

// User Management
import ListUserPage from "@/pages/User/list/ListUserPage.vue";

// Company List
import CompanyList from "@/pages/Company/List.vue";

// Company Management
import CompanyForm from "@/pages/Company/Form.vue";

// Fund List
import FundList from "@/pages/Fund/FundList.vue";

// Fund Management
import FundForm from "@/pages/Fund/FundForm.vue";

// Loan List
import LoanList from "@/pages/Loan/List.vue";

// Loan Management
import LoanForm from "@/pages/Loan/Form.vue";

// AllocatedLoan List
import AllocatedLoanList from "@/pages/AllocatedLoan/List.vue";

// AllocatedLoan Management
import AllocatedLoanViewForm from "@/pages/AllocatedLoan/Form.vue";

// AllocatedLoan CreateForm
import AllocatedLoanCreateForm from "@/pages/AllocatedLoan/Create.vue";

// AllocatedLoanInstallment CreateForm
import AllocatedLoanInstallmentCreateForm from "@/pages/AllocatedLoanInstallment/Create.vue";

// AllocatedLoanInstallment CreateForm
import AllocatedLoanInstallmentAddPaymentForm from "@/pages/AllocatedLoanInstallment/AddPayment";

// Transaction List
import TransactionList from "@/pages/Transaction/List.vue";

// Transaction Management
import TransactionForm from "@/pages/Transaction/Form.vue";

// PeriodicProcesses PaymentOfPayrollDeductions
import PaymentOfPayrollDeductions from "@/pages/PeriodicProcesses/PaymentOfPayrollDeductions";

// Pages
import RtlSupport from "@/pages/Dashboard/Pages/RtlSupport.vue";
import Login from "@/pages/Dashboard/Pages/Login.vue";
import Register from "@/pages/Dashboard/Pages/Register.vue";

// Components pages
import Notifications from "@/pages/Dashboard/Components/Notifications.vue";
import Icons from "@/pages/Dashboard/Components/Icons.vue";
import Typography from "@/pages/Dashboard/Components/Typography.vue";

// TableList pages
import RegularTables from "@/pages/Dashboard/Tables/RegularTables.vue";

// Maps pages
import FullScreenMap from "@/pages/Dashboard/Maps/FullScreenMap.vue";

//import middleware
import auth from "@/middleware/auth";
import guest from "@/middleware/guest";

let userMenu = {
    path: "/user",
    component: DashboardLayout,
    name: "User",
    children: [
        {
            path: "list",
            name: "User.List",
            components: {default: ListUserPage},
            meta: {
                rtlActive: true,
                displayName: "لیست کاربران",
                middleware: auth
            }
        },
        {
            path: "create",
            name: "User.Create",
            components: {default: UserProfile},
            meta: {
                rtlActive: true,
                displayName: "ساخت کاربر جدید",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "User.Show",
            components: {default: UserProfile},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات کاربر",
                middleware: auth
            }
        }
    ]
};

let companiesMenu = {
    path: "/company",
    name: "Company",
    component: DashboardLayout,
    children: [
        {
            path: "list",
            name: "Company.List",
            components: {default: CompanyList},
            meta: {
                rtlActive: true,
                displayName: "لیست شرکت ها",
                middleware: auth
            }
        },
        {
            path: "create",
            name: "Company.Create",
            components: {default: CompanyForm},
            meta: {
                rtlActive: true,
                displayName: "ساخت شرکت جدید",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "Company.Show",
            components: {default: CompanyForm},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات شرکت",
                middleware: auth
            }
        }
    ]
};

let fundsMenu = {
    path: "/fund",
    name: "Fund",
    component: DashboardLayout,
    children: [
        {
            path: "list",
            name: "Fund.List",
            components: {default: FundList},
            meta: {
                rtlActive: true,
                displayName: "لیست صندوق ها",
                middleware: auth
            }
        },
        {
            path: "create",
            name: "Fund.Create",
            components: {default: FundForm},
            meta: {
                rtlActive: true,
                displayName: "ساخت صندوق جدید",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "Fund.Show",
            components: {default: FundForm},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات صندوق",
                middleware: auth
            }
        }
    ]
};

let loansMenu = {
    path: "/loan",
    name: "Loan",
    component: DashboardLayout,
    children: [
        {
            path: "list",
            name: "Loan.List",
            components: {default: LoanList},
            meta: {
                rtlActive: true,
                displayName: "لیست وام ها",
                middleware: auth
            }
        },
        {
            path: "create",
            name: "Loan.Create",
            components: {default: LoanForm},
            meta: {
                rtlActive: true,
                displayName: "ساخت وام جدید",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "Loan.Show",
            components: {default: LoanForm},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات وام",
                middleware: auth
            }
        }
    ]
};

let allocatedLoansMenu = {
    path: "/allocated_loan",
    name: "AllocatedLoan",
    component: DashboardLayout,
    children: [
        {
            path: "list",
            name: "AllocatedLoan.List",
            components: {default: AllocatedLoanList},
            meta: {
                rtlActive: true,
                displayName: "لیست وام های تخصیص داده شده",
                middleware: auth
            }
        },
        {
            path: "create",
            name: "AllocatedLoan.Create",
            components: {default: AllocatedLoanCreateForm},
            meta: {
                rtlActive: true,
                displayName: "تخصیص وام جدید",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "AllocatedLoan.Show",
            components: {default: AllocatedLoanViewForm},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات وام تخصیص داده شده",
                middleware: auth
            }
        }
    ]
};

let allocatedLoanInstallmentsMenu = {
    path: "/allocated_loan_installment",
    name: "AllocatedLoanInstallment",
    component: DashboardLayout,
    children: [
        {
            path: "list",
            name: "AllocatedLoanInstallment.List",
            components: {default: AllocatedLoanList},
            meta: {
                rtlActive: true,
                displayName: "لیست قسط های تعریف شده",
                middleware: auth
            }
        },
        {
            path: ":allocated_loan_id/create",
            name: "AllocatedLoanInstallment.Create",
            components: {default: AllocatedLoanInstallmentCreateForm},
            meta: {
                rtlActive: true,
                displayName: "تعریف قسط جدید",
                middleware: auth
            }
        },{
            path: ":allocated_loan_id/:allocated_loan_installment_id/add_payment",
            name: "AllocatedLoanInstallment.AddPayment",
            components: {default: AllocatedLoanInstallmentAddPaymentForm},
            meta: {
                rtlActive: true,
                displayName: "پرداختی جدید برای قسط",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "AllocatedLoanInstallment.Show",
            components: {default: AllocatedLoanViewForm},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات قسط تعریف شده",
                middleware: auth
            }
        }
    ]
};

let periodicProcessesMenu = {
    path: "/periodic_processes",
    name: "PeriodicProcesses",
    component: DashboardLayout,
    children: [
        {
            path: "payment_of_payroll_deductions",
            name: "PaymentOfPayrollDeductions",
            components: {default: PaymentOfPayrollDeductions},
            meta: {
                rtlActive: true,
                displayName: "پرداخت اقساط کسر از حقوق",
                middleware: auth
            }
        }
    ]
};

let transactionMenu = {
    path: "/transactions",
    name: "Transaction",
    component: DashboardLayout,
    children: [
        {
            path: "list",
            name: "Transaction.List",
            components: {default: TransactionList},
            meta: {
                rtlActive: true,
                displayName: "لیست تراکنش ها",
                middleware: auth
            }
        },
        {
            path: "create",
            name: "Transaction.Create",
            components: {default: TransactionForm},
            meta: {
                rtlActive: true,
                displayName: "تعریف تراکنش جدید",
                middleware: auth
            }
        },
        {
            path: ":id",
            name: "Transaction.Show",
            components: {default: TransactionForm},
            meta: {
                rtlActive: true,
                displayName: "اطلاعات تراکنش",
                middleware: auth
            }
        }
    ]
};




let componentsMenu = {
    path: "/components",
    component: DashboardLayout,
    redirect: "/components/notification",
    name: "Components",
    children: [
        {
            path: "table",
            name: "Table",
            components: {default: RegularTables},
            meta: {
                rtlActive: true,
                middleware: auth
            }
        },
        {
            path: "typography",
            name: "Typography",
            components: {default: Typography},
            meta: {
                rtlActive: true,
                middleware: auth
            }
        },
        {
            path: "icons",
            name: "Icons",
            components: {default: Icons},
            meta: {
                rtlActive: true,
                middleware: auth
            }
        },
        {
            path: "maps",
            name: "Maps",
            meta: {
                hideContent: true,
                hideFooter: true,
                navbarAbsolute: true,
                rtlActive: true,
                middleware: auth
            },
            components: {default: FullScreenMap}
        },
        {
            path: "notifications",
            name: "Notifications",
            components: {default: Notifications},
            meta: {
                rtlActive: true,
                middleware: auth
            },
        },
        {
            path: "rtl",
            name: "راست چین",
            meta: {
                rtlActive: true,
                middleware: auth
            },
            components: {default: RtlSupport}
        }
    ]
};


let authPages = {
    path: "/",
    component: AuthLayout,
    name: "Authentication",
    children: [
        {
            path: "/login",
            name: "Login",
            component: Login,
            meta: {middleware: guest}
        },
        {
            path: "/register",
            name: "Register",
            component: Register,
            meta: {middleware: guest}
        }
    ]
};

const routes = [
    // {
    //     path: "/",
    //     redirect: "/dashboard",
    //     name: "Home"
    // },
    {
        path: "/",
        component: DashboardLayout,
        meta: {
            rtlActive: true,
            middleware: auth
        },
        children: [
            {
                path: "/",
                name: "پیشخوان",
                components: {default: Dashboard},
                meta: {
                    rtlActive: true,
                    middleware: auth
                }
            }
        ]
    },
    userMenu,
    companiesMenu,
    fundsMenu,
    loansMenu,
    allocatedLoansMenu,
    allocatedLoanInstallmentsMenu,
    transactionMenu,
    periodicProcessesMenu,
    componentsMenu,
    authPages
];

export default routes;
