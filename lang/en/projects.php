<?php

// lang/en/projects.php

return [
    // Page Titles
    'page_title' => 'Projects',
    'project_list' => 'Project List',
    'add_project' => 'Add Project',
    'add_new_project' => 'Add New Project',
    'edit_project' => 'Edit Project',
    'project_detail' => 'Project Details',
    'preview_export' => 'Export Preview',
    
    // Alerts
    'success' => 'Success!',
    'error' => 'Error!',
    
    // Stats Cards
    'total_projects' => 'Total Projects',
    'progress' => 'In Progress',
    'pending' => 'Pending',
    'done' => 'Completed',
    
    // Filter Section
    'filter_search' => 'Filter & Search',
    'show_filter' => 'Show Filter',
    'hide_filter' => 'Hide Filter',
    'active_filters' => 'Active Filters:',
    'search_project' => 'Search Project',
    'search_placeholder' => 'Project name, PIC, or division...',
    'all_status' => 'All Statuses',
    'all_urgency' => 'All Urgency Levels',
    'all_divisions' => 'All Divisions',
    'apply_filter' => 'Apply Filter',
    'reset_filter' => 'Reset Filter',
    'search_label' => 'Search',
    'status_label' => 'Status',
    'urgency_label' => 'Urgency',
    'division_label' => 'Division',
    
    // Table Headers
    'no' => 'No',
    'project_name' => 'Project Name',
    'owner' => 'Owner',
    'pic' => 'PIC',
    'urgency' => 'Urgency',
    'status' => 'Status',
    'target' => 'Target',
    'actions' => 'Actions',
    'date' => 'Date',
    
    // Sorting
    'showing' => 'Showing',
    'of' => 'of',
    'projects_text' => 'projects',
    'sort_by' => 'Sort by:',
    'name' => 'Name',
    'initiation_date' => 'Date',
    
    // Empty States
    'no_projects_found' => 'No projects found',
    'try_different_filter' => 'Try changing the filters or resetting your search',
    'no_projects_yet' => 'No projects yet',
    'not_assigned_pic' => 'You are not assigned as a PIC for any project.',
    'start_first_project' => 'Start by creating your first project.',
    
    // Buttons
    'back' => 'Back',
    'back_to_detail' => 'Back to Details',
    'cancel' => 'Cancel',
    'save' => 'Save Project',
    'update' => 'Update Project',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'download_pdf' => 'Download PDF',
    'no_data' => 'No Data',
    
    // Form Labels
    'initiation_date' => 'Initiation Date',
    'target_implementation' => 'Implementation Target',
    'quarter' => 'Quarter',
    'project_name_label' => 'Project Name',
    'urgency_label' => 'Urgency',
    'project_nature' => 'Project Nature',
    'select_nature' => 'Select Project Nature',
    'others' => 'Others',
    'enter_other_nature' => 'Enter another project nature...',
    'description' => 'Description',
    'project_description' => 'Project Description',
    'project_owner' => 'Project Owner (Division)',
    'select_division' => 'Select Division',
    'pic_project' => 'Project PIC',
    'select_pic' => 'Select PIC',
    'select_status' => 'Select Status',
    'select_urgency' => 'Select Urgency',
    'enter_project_name' => 'Enter the project name',
    'enter_description' => 'Enter the project description (optional)',
    'required_field' => 'is required',
    
    // Validation Messages
    'urgency_required' => 'Urgency must be selected',
    'nature_required' => 'Project nature must be selected',
    'division_required' => 'Division must be selected',
    'pic_required' => 'Project PIC must be selected',
    'status_required' => 'Status must be selected',
    
    // Urgency Levels
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High',
    'very_high' => 'Very High',
    
    // Status
    'in_progress' => 'In Progress',
    'pending_status' => 'Pending',
    'completed' => 'Completed',
    
    // Project Nature Options
    'rbb' => 'RBB',
    'non_rbb' => 'Non RBB',
    'regulator' => 'Regulator',
    'official' => 'Official',
    'tl_audit' => 'Audit Follow-Up',
    'kedinasan' => 'Officialdom',
    
    // Project Detail
    'project_information' => 'Project Information',
    'nature' => 'Nature',
    
    // Activities Section
    'activity_list' => 'Activity List',
    'add_activity' => 'Add Activity',
    'pgb' => 'Development Section',
    'pkj' => 'Licensing Section',
    'total_activities' => 'total activities',
    'no_activities_pgb' => 'No Development activities yet',
    'no_activities_pkj' => 'No Licensing activities yet',
    'start_first_activity' => 'Start by adding the first activity for this project',
    'toggle_to' => 'Try switching to',
    'to_see_other' => 'to view other activities',
    'no_activities_added' => 'No activities have been added for this section yet',
    'add_first_activity' => 'Add First Activity',
    
    // Activity Table
    'activity_name' => 'Activity Name',
    'attachment' => 'Attachment',
    
    // Confirm Dialogs
    'confirm_delete_project' => 'Are you sure you want to delete this project? All related activities will also be deleted!',
    'confirm_delete_activity' => 'Are you sure you want to delete this activity?',
    
    // Export Preview
    'project_report' => 'Project Report',
    'preview_export_data' => 'Preview the data to be exported to PDF',
    'applied_filters' => '🔍 Applied Filters:',
    'data_projects' => '📋 Project Data',
    'warning_large_export' => '⚠️ <strong>Warning:</strong> Exporting will generate a large file (:count projects). Consider adding filters to narrow down the data.',
    'no_export_data' => 'No data available for export',
    'adjust_filter' => 'Try adjusting your filters',
    
    // Filter Keys for Export
    'filter_search_key' => '🔍 Search:',
    'filter_status_key' => '📊 Status:',
    'filter_division_key' => '🏢 Division:',
    'filter_urgency_key' => '⚡ Urgency:',
    'filter_period_key' => '📅 Period:',
    'filter_range_key' => '📅 Range:',
    'filter_from_key' => '📅 From:',
    'filter_to_key' => '📅 To:',

    // Export Section
    'export_project' => 'Export Project PDF',
    'export_project_description' => 'Export project along with all activities (PGB & PKJ) to PDF format',
    'printed_on' => 'Printed on',
    'exported_by' => 'Exported by',
    'document_generated' => 'Document generated on',
    'system_name' => 'Project Management System',
    'person_in_charge' => 'Person in Charge',

    // PIC Selection
    'select_pic_placeholder' => 'Select PIC (multiple selection allowed)',
    'selected' => 'selected',
    'no_available_users' => 'No users available for this section',
    'no_results_found' => 'No results found',
    'searching' => 'Searching...',

    // ✅ NEW: Pengawas Project
    'pengawas_project' => 'Project Supervisor',
    'supervisor_project' => 'Supervisor',
    'overseer' => 'Overseer',
    
    // Existing translations
    'pic_project' => 'Project PIC',
    'project_owner' => 'Project Owner',
    'person_in_charge' => 'Person In Charge',
    'created_by_kadiv_info' => 'This project was created by Kadiv, so there is no separate Supervisor',

    'select_pic_placeholder' => 'Select Project PIC (can choose more than 1)',
    'search_pic' => 'Search PIC...',
    'no_results' => 'No results found',
    'searching' => 'Searching',

    // PIC Selection Hints
    'pic_hint_kabag_pgb' => '💡 Head of PGB can select from <span class="font-semibold text-green-700">PGB</span> and <span class="font-semibold text-purple-700">PKJ</span> (excluding Kadiv)',
    'pic_hint_perizinan' => '💡 PKJ can select all users except Supervisi/Kadiv',
    'pic_hint_supervisi' => '🔰 Super Admin can select from all departments (PGB & PKJ)',
];