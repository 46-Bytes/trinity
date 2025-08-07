<?php
namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class SubscriptionStatus extends Component {
    public $subscription;

    public function mount() {
        $this->subscription = auth()->user()->subscription('default');
    }

    public function pause() {
        if ($this->subscription && !$this->subscription->onGracePeriod()) {
            $this->subscription->cancel(); // Cancels the subscription with a grace period
            session()->flash('success', 'Subscription paused. Account will remain active until ' . Carbon::createFromTimestamp($this->subscription->asStripeSubscription()->current_period_end)->toFormattedDateString());
            $this->subscription->refresh();
        }
    }

    public function resume() {
        if ($this->subscription && $this->subscription->onGracePeriod()) {
            $this->subscription->resume();
            session()->flash('success', 'Subscription resumed successfully.');
            $this->subscription->refresh();
        }
    }
    public function cancel() {
        if ($this->subscription) {
            $this->subscription->cancelNow(); // Cancels immediately without a grace period
            session()->flash('success', 'Subscription canceled successfully.');
            $this->subscription->refresh();
        }
    }

    public function render() {
        return view('livewire.subscription-status', [
            'subscription' => $this->subscription,
        ]);
    }
}
