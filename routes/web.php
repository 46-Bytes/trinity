<?php

use App\Http\Controllers\{AccountController,
    AdminController,
    ChatController,
    CustomRegistrationController,
    DashboardController,
    DiagnosticController,
    FeedbackController,
    FormController,
    FormEntryController,
    FormEntryLoadController,
    MediaController,
    NoteController,
    OrgController,
    ProductController,
    SettingController,
    StripeWebhookController,
    SubscriptionController,
    TaskController,
    UserController
};
use App\Services\GPTService;
use Illuminate\Support\Facades\Route;

// Public Routes
// HOME LANDING PAGE
Route::get('/', function () {
    return view('auth.login');
});
Route::get('/support', function () {
    return view('support.index');
});

Route::get('/test-gpt', function (GPTService $gptService) {
    return convertMarkdownToHtml(
        $gptService->generateResponse(['Describe an Automated Online Business Advisor'])['response']
    );
});

// SignUp Routes
Route::get('/subscribe', [SubscriptionController::class, 'showForm'])->name('subscription.form');
Route::post('/subscribe', [SubscriptionController::class, 'processForm'])->name('subscription.process');
Route::post('/custom-register', [CustomRegistrationController::class, 'register']);
//Route::post('/register', [CustomRegistrationController::class, 'register'])->name('register');
//Route::get('/register', [CustomRegistrationController::class, 'showRegistrationForm'])->name('register'); // TODO: redirect to home page #pricing if no querystring
Route::post('/register', [CustomRegistrationController::class, 'register_duplicate'])->name('register_duplicate');
Route::get('/register', [CustomRegistrationController::class, 'showRegistrationFormDuplicate'])->name('register');
Route::post('/validate-coupon', [CustomRegistrationController::class, 'validateCoupon']);

// Authenticated Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['auth', 'can:run-commands'])
        ->get('/form-entry/{user}/load-in-progress', [FormEntryLoadController::class, 'inProgress'])
        ->name('form-entry.load.in-progress');

    // Account Routes
    Route::get('account', [AccountController::class, 'show'])->name('account.show');
    Route::redirect('billing', '/account#billing-tab')->name('account.billing');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::redirect('prompts', '/settings')->name('prompts');
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('orgs', [AdminController::class, 'orgs'])->name('orgs');
    });

    // Resource Routes
    Route::resource('users', UserController::class)->except(['index']);
    Route::resource('forms', FormController::class);
    Route::resource('form_entries', FormEntryController::class);
    Route::resource('notes', NoteController::class);
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::resource('files', MediaController::class)->except(['show']);
    Route::resource('feedback', FeedbackController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('orgs', OrgController::class);

    // Notes Routes
    Route::post('/notes/dashStore', [NoteController::class, 'dashStore'])->name('notes.dashStore');
    Route::post('/notes/{note}/toggle-pin', [NoteController::class, 'togglePin'])->name('notes.togglePin');
    Route::post('/notes/{note}/update-color', [NoteController::class, 'updateColor'])->name('notes.updateColor');

    // Tasks Routes
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Diagnostic Routes
    Route::get('/diagnostics', [DiagnosticController::class, 'index'])->name('diagnostic.index');
    Route::get('/diagnostic/create', [DiagnosticController::class, 'create'])->name('diagnostic.create');
    Route::get('/diagnostic/clone/{id}', [DiagnosticController::class, 'clone'])->name('diagnostic.clone');
    Route::get('/diagnostic/download/{id}', [MediaController::class, 'downloadDiagnostic'])->name('diagnostic.download');
//    Route::get('/diagnostic', [DiagnosticController::class, 'index'])->name('diagnostic.index');
    Route::get('/diagnostic/{Id}', [DiagnosticController::class, 'show'])->name('diagnostic.show');
//    Route::post('/diagnostic/{messageId}/create-task', [DiagnosticController::class, 'createTaskFromMessage'])->name('diagnostic.createTaskFromMessage');
//    Route::post('/diagnostic/{messageId}/create-note', [DiagnosticController::class, 'createNoteFromMessage'])->name('diagnostic.createNoteFromMessage');
    Route::post('/diagnostic/upload-file', [DiagnosticController::class, 'uploadFile'])->name('diagnostic.uploadFile');
    Route::post('/diagnostic/save-entry', [DiagnosticController::class, 'saveFormEntry'])->name('diagnostic.saveFormEntry');

    // Media Routes
    Route::get('/files/download-diagnostic/{id}', [MediaController::class, 'downloadDiagnostic'])->name('files.downloadDiagnostic');
    Route::get('/files/download-latest-diagnostic', [MediaController::class, 'downloadLatestDiagnostic'])->name('files.downloadLatestDiagnostic');
    Route::post('/files/upload', [MediaController::class, 'upload'])->name('files.upload');
    Route::post('form/files/upload', [MediaController::class, 'formFilesUpload'])->name('formFiles.upload');
    Route::get('files/download/{id}', [MediaController::class, 'downloadFileById'])
        ->name('download.file');

    // Chat Routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/conversation/{conversation}', [ChatController::class, 'showConversation'])->name('showConversation');
        Route::post('/{conversationId}/send', [ChatController::class, 'sendMessage'])->name('sendMessage');
        Route::post('/create', [ChatController::class, 'createConversation'])->name('createConversation');
        Route::post('/{messageId}/create-task', [ChatController::class, 'createTaskFromMessage'])->name('createTaskFromMessage');
        Route::post('/{messageId}/create-note', [ChatController::class, 'createNoteFromMessage'])->name('createNoteFromMessage');
    });

    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');
    Route::post('/subscription/pause', [SubscriptionController::class, 'resume'])->name('subscription.pause');
    // Route::post('/subscription/swap', [SubscriptionController::class, 'swap'])->name('subscription.swap');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('billing.portal');
    //Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook'); // Laravel Cashier - Stripe Webhook

    Route::get('/checkout', [SubscriptionController::class, 'showSubscriptionForm'])->name('checkout');
    Route::post('/checkout', [SubscriptionController::class, 'processSubscription'])->name('checkout');

    // Product Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});
