<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $panel_id
 * @property null|Carbon $created_at
 */
class PanelDeployment extends Model
{
    use SoftDeletes;

    /** @var null|PanelDeployment */
    private $prev;

    /** @var null|PanelDeployment */
    private $next;

    protected $fillable = [
        'panel_id',
        'deployment_id',
        'status',
        'file_path',
        'deployment_log',
    ];

    public function panel(): BelongsTo
    {
        return $this->belongsTo(Panel::class);
    }

    public function addNewMessage(string $message): void
    {
        $log = $this->fresh();

        if (!$log) {
            return;
        }

        $log->deployment_log .= $message;
        $log->save();
    }

    /**
     * Get the same panel precedent deployment.
     */
    public function prev(): ?PanelDeployment
    {
        // TODO is possible to replace this query with a BelongsTo relation?
        if ($this->prev === null) {
            $this->prev = self::query()
                ->where('panel_id', '=', $this->panel_id)
                ->where('created_at', '<', $this->created_at)
                ->orderByDesc('created_at')->first();
            if (!$this->prev instanceof PanelDeployment) {
                $this->prev = null;
            }
        }

        return $this->prev;
    }
}
