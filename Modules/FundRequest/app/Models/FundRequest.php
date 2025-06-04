<?php

namespace Modules\FundRequest\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Role\App\Models\User; // Assuming this is the correct User model path
use RingleSoft\LaravelProcessApproval\Traits\Approvable;
use RingleSoft\LaravelProcessApproval\Contracts\ApprovableModel;
use Spatie\Permission\Models\Role; // Import Spatie's Role model

class FundRequest extends Model implements ApprovableModel
{
    use HasFactory, Approvable; // Corrected trait name

    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'status',
    ];

    /**
     * Define the relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the approval steps for the fund request.
     *
     * @return \RingleSoft\LaravelProcessApproval\Support\ApprovalStep[]
     */
    public function getApprovalSteps(): array
    {
        $managerRole = Role::findByName('Manager', config('auth.defaults.guard'));
        $directorRole = Role::findByName('Director', config('auth.defaults.guard'));

        // Ensure roles exist to prevent errors
        if (!$managerRole || !$directorRole) {
            // Log error or throw exception, as this is a critical configuration issue
            // For now, return an empty array or throw an exception
            throw new \Exception("Required roles 'Manager' or 'Director' not found.");
        }

        return [
            \RingleSoft\LaravelProcessApproval\Support\ApprovalStep::make('pending_level1_approval')
                ->label('Manager Approval')
                ->description('Requires approval from a Manager.')
                ->roleId($managerRole->id) // Pass the Role ID
                ->nextStatus('pending_level2_approval')
                ->approvers(function () use ($managerRole) {
                    // This callable can still be used for other purposes like notifications
                    return User::role($managerRole->name)->pluck('id')->toArray();
                }),
            \RingleSoft\LaravelProcessApproval\Support\ApprovalStep::make('pending_level2_approval')
                ->label('Director Approval')
                ->description('Requires approval from a Director.')
                ->roleId($directorRole->id) // Pass the Role ID
                ->nextStatus('approved') // Final approved status
                ->approvers(function () use ($directorRole) {
                    return User::role($directorRole->name)->pluck('id')->toArray();
                }),
        ];
    }

    /**
     * Get the column name that stores the status of the model.
     * @return string
     */
    public function approvalStatusColumn(): string
    {
        return 'status';
    }

    /**
     * Get the foreign key for the user who created the model.
     * @return string
     */
    public function approvalUserForeignKey(): string
    {
        return 'user_id';
    }
/**
     * Callback action when the approval process is completed.
     *
     * @param \RingleSoft\LaravelProcessApproval\Models\ProcessApproval $approval The last approval action.
     * @return bool True if the callback action was successful, false otherwise.
     */
    public function onApprovalCompleted(\RingleSoft\LaravelProcessApproval\Models\ProcessApproval $approval): bool
    {
        // Example: Log completion, notify user, update other related models, etc.
        // \Illuminate\Support\Facades\Log::info("FundRequest #{$this->id} has been fully approved.");
        // $this->update(['status' => 'fully_approved_and_processed']); // Or a similar final status

        // For now, just return true to indicate success.
        return true;
    }
}
