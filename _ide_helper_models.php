<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $tanggal_mulai
 * @property \Illuminate\Support\Carbon|null $tanggal_selesai
 * @property string $nama_aktivitas
 * @property string $jenis_kegiatan
 * @property string $status
 * @property string|null $deskripsi
 * @property string|null $lampiran
 * @property string|null $lampiran_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereJenisKegiatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLampiran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLampiranLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereNamaAktivitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUserId($value)
 */
	class Activity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $description
 * @property array<array-key, mixed>|null $properties
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $action_color
 * @property-read mixed $action_label
 * @property-read mixed $model_name
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $loggable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog action($action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog dateRange($startDate, $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog modelType($modelType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_divisi
 * @property string|null $kode_divisi
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $display_name
 * @property-read mixed $short_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereKodeDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereNamaDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereUpdatedAt($value)
 */
	class Division extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $tanggal_input
 * @property string|null $due_date
 * @property string $nama_project
 * @property string $urgency
 * @property string $sifat_pekerjaan
 * @property string $deskripsi
 * @property string $pemilik_project
 * @property string $jenis_kegiatan
 * @property string $status
 * @property string|null $lampiran
 * @property int $locked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereJenisKegiatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereLampiran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereNamaProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log wherePemilikProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereSifatPekerjaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereTanggalInput($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereUrgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereUserId($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $tanggal_inisiasi
 * @property string $target_implementasi
 * @property string $nama_project
 * @property string $urgensi
 * @property string $sifat_project
 * @property string|null $deskripsi
 * @property int $pemilik_project_id
 * @property int $pic_proyek_id
 * @property int|null $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Division $pemilikProject
 * @property-read \App\Models\User $picProyek
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereNamaProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project wherePemilikProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project wherePicProyekId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereSifatProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTanggalInisiasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTargetImplementasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUrgensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUserId($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $requested_role
 * @property string|null $requested_bagian
 * @property string $deskripsi
 * @property string|null $dokumen_path
 * @property string $status
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read mixed $days_old
 * @property-read mixed $formatted_date
 * @property-read mixed $status_color
 * @property-read \App\Models\User|null $reviewer
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereDokumenPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereRequestedBagian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereRequestedRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleRequest whereUserId($value)
 */
	class RoleRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $role
 * @property string|null $bagian
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $createdProjects
 * @property-read int|null $created_projects_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projectsAsPic
 * @property-read int|null $projects_as_pic_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleRequest> $roleRequests
 * @property-read int|null $role_requests_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBagian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

