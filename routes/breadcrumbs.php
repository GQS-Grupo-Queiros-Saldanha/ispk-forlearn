<?php

// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('home'));
});

//Admin
Breadcrumbs::for('admin', function ($trail) {
    $trail->push('Admin', route('home'));
});

//======================================================================================================================
// Roles
//======================================================================================================================

// Home > Roles
Breadcrumbs::for('roles', function ($trail) {
    $trail->parent('admin');
    $trail->push(__('header.roles'), route('roles.index'));
});

// Home > Roles > Create
Breadcrumbs::for('roles.create', function ($trail) {
    $trail->parent('roles');
    $trail->push(__('common.create'), route('roles.create'));
});

// Home > Roles > [Role]
Breadcrumbs::for('roles.show', function ($trail, $role) {
    $trail->parent('roles');
    $trail->push($role->name, route('roles.show', $role->id));
});

// Home > Roles > [Role] > Permissions
Breadcrumbs::for('roles.permissions', function ($trail, $item) {
    $trail->parent('roles');
    $trail->push(__('common.permissions') . ' - ' . $item->name, route('roles.permissions', $item->id));
});

// Home > Roles > Edit - [Role]
Breadcrumbs::for('roles.edit', function ($trail, $item) {
    $trail->parent('roles');
    $trail->push(__('common.edit') . ' - ' . $item->name, route('roles.edit', $item->id));
});


//======================================================================================================================
// Users
//======================================================================================================================

// Home > Users
Breadcrumbs::for('users', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.users'), route('users.index'));
});

// Home > Users > Create
Breadcrumbs::for('users.create', function ($trail) {
    $trail->parent('users');
    $trail->push(__('common.create'), route('users.create'));
});

// Home > Users > [User]
Breadcrumbs::for('users.show', function ($trail, $user) {
    $trail->parent('users');
    $trail->push($user->name, route('users.show', $user->id));
});

// Home > Users > Edit - [User]
Breadcrumbs::for('users.edit', function ($trail, $user) {
    $trail->parent('users');
    $trail->push(__('common.edit') . ' - ' . $user->name, route('users.edit', $user->id));
});

// Home > Users > [User] > Roles
Breadcrumbs::for('users.roles', function ($trail, $item) {
    $trail->parent('users');
    $trail->push(__('Users::roles.roles') . ' - ' . $item->name, route('users.index'));
});

// Home > Users > [User] > Permissions
Breadcrumbs::for('users.permissions', function ($trail, $item) {
    $trail->parent('users');
    $trail->push(__('Users::permissions.permissions') . ' - ' . $item->name, route('users.index'));
});

//======================================================================================================================
// Permissions
//======================================================================================================================

// Home > Permissions
Breadcrumbs::for('permissions', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.permissions'), route('permissions.index'));
});

// Home > Permissions > Create
Breadcrumbs::for('permissions.create', function ($trail) {
    $trail->parent('permissions');
    $trail->push(__('common.create'), route('permissions.create'));
});

// Home > Permissions > [Permission]
Breadcrumbs::for('permissions.show', function ($trail, $item) {
    $trail->parent('permissions');
    $trail->push($item->name, route('permissions.show', $item->id));
});

// Home > Permissions > Edit - [Permission]
Breadcrumbs::for('permissions.edit', function ($trail, $item) {
    $trail->parent('permissions');
    $trail->push(__('common.edit') . ' - ' . $item->name, route('permissions.edit', $item->id));
});

//======================================================================================================================
// Professions
//======================================================================================================================

// Home > Professions
Breadcrumbs::for('professions', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.professions'), route('professions.index'));
});

// Home > Professions > Create
Breadcrumbs::for('professions.create', function ($trail) {
    $trail->parent('professions');
    $trail->push(__('common.create'), route('professions.create'));
});

// Home > Professions > [Profession]
Breadcrumbs::for('professions.show', function ($trail, $item) {
    $trail->parent('professions');
    $trail->push($item->code, route('professions.show', $item->id));
});

// Home > Professions > Edit - [Profession]
Breadcrumbs::for('professions.edit', function ($trail, $item) {
    $trail->parent('professions');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('professions.edit', $item->id));
});

//======================================================================================================================
// Professional states
//======================================================================================================================

// Home > Professional states
Breadcrumbs::for('professional-states', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.professional_states'), route('professional-states.index'));
});

// Home > Professional states > Create
Breadcrumbs::for('professional-states.create', function ($trail) {
    $trail->parent('professional-states');
    $trail->push(__('common.create'), route('professional-states.create'));
});

// Home > Professional states > [Professional state]
Breadcrumbs::for('professional-states.show', function ($trail, $item) {
    $trail->parent('professional-states');
    $trail->push($item->code, route('professional-states.show', $item->id));
});

// Home > Professional states > Edit - [Professional state]
Breadcrumbs::for('professional-states.edit', function ($trail, $item) {
    $trail->parent('professional-states');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('professional-states.edit', $item->id));
});

//======================================================================================================================
// Parameters
//======================================================================================================================

// Home > Parameters
Breadcrumbs::for('parameters', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.parameters'), route('parameters.index'));
});

// Home > Parameters > Create
Breadcrumbs::for('parameters.create', function ($trail) {
    $trail->parent('parameters');
    $trail->push(__('common.create'), route('parameters.create'));
});

// Home > Parameters > [Parameter]
Breadcrumbs::for('parameters.show', function ($trail, $item) {
    $trail->parent('parameters');
    $trail->push($item->code, route('parameters.show', $item->id));
});

// Home > Parameters > Edit - [Parameter]
Breadcrumbs::for('parameters.edit', function ($trail, $item) {
    $trail->parent('parameters');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('parameters.edit', $item->id));
});


//======================================================================================================================
// Parameter groups
//======================================================================================================================

// Home > Parameter groups
Breadcrumbs::for('parameter-groups', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.parameter_groups'), route('parameter-groups.index'));
});

// Home > Parameter groups > Create
Breadcrumbs::for('parameter-groups.create', function ($trail) {
    $trail->parent('parameter-groups');
    $trail->push(__('common.create'), route('parameter-groups.create'));
});

// Home > Parameter groups > [Parameter]
Breadcrumbs::for('parameter-groups.show', function ($trail, $item) {
    $trail->parent('parameter-groups');
    $trail->push($item->code, route('parameter-groups.show', $item->id));
});

// Home > Parameter groups > Edit - [Parameter]
Breadcrumbs::for('parameter-groups.edit', function ($trail, $item) {
    $trail->parent('parameter-groups');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('parameter-groups.edit', $item->id));
});

//======================================================================================================================
// Degree levels
//======================================================================================================================

// Home > Degree levels
Breadcrumbs::for('degree-levels', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.degree_levels'), route('degree-levels.index'));
});

// Home > Degree levels > Create
Breadcrumbs::for('degree-levels.create', function ($trail) {
    $trail->parent('degree-levels');
    $trail->push(__('common.create'), route('degree-levels.create'));
});

// Home > Degree levels > [Document type]
Breadcrumbs::for('degree-levels.show', function ($trail, $item) {
    $trail->parent('degree-levels');
    $trail->push($item->code, route('degree-levels.show', $item->id));
});

// Home > Degree levels > Edit - [Document type]
Breadcrumbs::for('degree-levels.edit', function ($trail, $item) {
    $trail->parent('degree-levels');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('degree-levels.edit', $item->id));
});

//======================================================================================================================
// Disciplines
//======================================================================================================================

// Home > Discipline areas
Breadcrumbs::for('disciplines', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.disciplines'), route('disciplines.index'));
});

// Home > Discipline areas > Create
Breadcrumbs::for('disciplines.create', function ($trail) {
    $trail->parent('disciplines');
    $trail->push(__('common.create'), route('disciplines.create'));
});

// Home > Discipline areas > [Discipline area]
Breadcrumbs::for('disciplines.show', function ($trail, $item) {
    $trail->parent('disciplines');
    $trail->push($item->code, route('disciplines.show', $item->id));
});

// Home > Discipline areas > Edit - [Discipline area]
Breadcrumbs::for('disciplines.edit', function ($trail, $item) {
    $trail->parent('disciplines');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('disciplines.edit', $item->id));
});

//======================================================================================================================
// Discipline areas
//======================================================================================================================

// Home > Discipline areas
Breadcrumbs::for('discipline-areas', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline_areas'), route('discipline-areas.index'));
});

// Home > Discipline areas > Create
Breadcrumbs::for('discipline-areas.create', function ($trail) {
    $trail->parent('discipline-areas');
    $trail->push(__('common.create'), route('discipline-areas.create'));
});

// Home > Discipline areas > [Discipline area]
Breadcrumbs::for('discipline-areas.show', function ($trail, $item) {
    $trail->parent('discipline-areas');
    $trail->push($item->code, route('discipline-areas.show', $item->id));
});

// Home > Discipline areas > Edit - [Discipline area]
Breadcrumbs::for('discipline-areas.edit', function ($trail, $item) {
    $trail->parent('discipline-areas');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('discipline-areas.edit', $item->id));
});

//======================================================================================================================
// Discipline periods
//======================================================================================================================

// Home > Discipline periods
Breadcrumbs::for('discipline-periods', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline_periods'), route('discipline-periods.index'));
});

// Home > Discipline periods > Create
Breadcrumbs::for('discipline-periods.create', function ($trail) {
    $trail->parent('discipline-periods');
    $trail->push(__('common.create'), route('discipline-periods.create'));
});

// Home > Discipline periods > [Discipline period]
Breadcrumbs::for('discipline-periods.show', function ($trail, $item) {
    $trail->parent('discipline-periods');
    $trail->push($item->code, route('discipline-periods.show', $item->id));
});

// Home > Discipline periods > Edit - [Discipline period]
Breadcrumbs::for('discipline-periods.edit', function ($trail, $item) {
    $trail->parent('discipline-periods');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('discipline-periods.edit', $item->id));
});

//======================================================================================================================
// Discipline profiles
//======================================================================================================================

// Home > Discipline profiles
Breadcrumbs::for('discipline-profiles', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline_profiles'), route('discipline-profiles.index'));
});

// Home > Discipline profiles > Create
Breadcrumbs::for('discipline-profiles.create', function ($trail) {
    $trail->parent('discipline-profiles');
    $trail->push(__('common.create'), route('discipline-profiles.create'));
});

// Home > Discipline profiles > [Discipline profile]
Breadcrumbs::for('discipline-profiles.show', function ($trail, $item) {
    $trail->parent('discipline-profiles');
    $trail->push($item->code, route('discipline-profiles.show', $item->id));
});

// Home > Discipline profiles > Edit - [Discipline profile]
Breadcrumbs::for('discipline-profiles.edit', function ($trail, $item) {
    $trail->parent('discipline-profiles');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('discipline-profiles.edit', $item->id));
});

//======================================================================================================================
// Discipline regimes
//======================================================================================================================

// Home > Discipline regimes
Breadcrumbs::for('discipline-regimes', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline_regimes'), route('discipline-regimes.index'));
});

// Home > Discipline regimes > Create
Breadcrumbs::for('discipline-regimes.create', function ($trail) {
    $trail->parent('discipline-regimes');
    $trail->push(__('common.create'), route('discipline-regimes.create'));
});

// Home > Discipline regimes > [Discipline regime]
Breadcrumbs::for('discipline-regimes.show', function ($trail, $item) {
    $trail->parent('discipline-regimes');
    $trail->push($item->code, route('discipline-regimes.show', $item->id));
});

// Home > Discipline regimes > Edit - [Discipline regime]
Breadcrumbs::for('discipline-regimes.edit', function ($trail, $item) {
    $trail->parent('discipline-regimes');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('discipline-regimes.edit', $item->id));
});

//======================================================================================================================
// Optional groups
//======================================================================================================================

// Home > Optional groups
Breadcrumbs::for('optional-groups', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.optional_groups'), route('optional-groups.index'));
});

// Home > Optional groups > Create
Breadcrumbs::for('optional-groups.create', function ($trail) {
    $trail->parent('optional-groups');
    $trail->push(__('common.create'), route('optional-groups.create'));
});

// Home > Optional groups > [Discipline regime]
Breadcrumbs::for('optional-groups.show', function ($trail, $item) {
    $trail->parent('optional-groups');
    $trail->push($item->code, route('optional-groups.show', $item->id));
});

// Home > Optional groups > Edit - [Discipline regime]
Breadcrumbs::for('optional-groups.edit', function ($trail, $item) {
    $trail->parent('optional-groups');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('optional-groups.edit', $item->id));
});

//======================================================================================================================
// GA - Departments
//======================================================================================================================
// Home > Degree levels
Breadcrumbs::for('departments', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.departments'), route('departments.index'));
});

// Home > Degree levels > Create
Breadcrumbs::for('departments.create', function ($trail) {
    $trail->parent('departments');
    $trail->push(__('common.create'), route('departments.create'));
});

// Home > Degree levels > [Document type]
Breadcrumbs::for('departments.show', function ($trail, $item) {
    $trail->parent('departments');
    $trail->push($item->code, route('departments.show', $item->id));
});

// Home > Degree levels > Edit - [Document type]
Breadcrumbs::for('departments.edit', function ($trail, $item) {
    $trail->parent('departments');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('departments.edit', $item->id));
});


//======================================================================================================================
// GA - Course cycles
//======================================================================================================================
// Home >Course cycles
Breadcrumbs::for('course-cycles', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.course_cycles'), route('course-cycles.index'));
});

// Home > Course cycles > Create
Breadcrumbs::for('course-cycles.create', function ($trail) {
    $trail->parent('course-cycles');
    $trail->push(__('common.create'), route('course-cycles.create'));
});

// Home > Course cycles > [Document type]
Breadcrumbs::for('course-cycles.show', function ($trail, $item) {
    $trail->parent('course-cycles');
    $trail->push($item->code, route('course-cycles.show', $item->id));
});

// Home > Course cycles > Edit - [Document type]
Breadcrumbs::for('course-cycles.edit', function ($trail, $item) {
    $trail->parent('course-cycles');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('course-cycles.edit', $item->id));
});


//======================================================================================================================
// GA - Course regimes
//======================================================================================================================
// Home > Course regimes
Breadcrumbs::for('course-regimes', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.course_regimes'), route('course-regimes.index'));
});

// Home > Course regimes > Create
Breadcrumbs::for('course-regimes.create', function ($trail) {
    $trail->parent('course-regimes');
    $trail->push(__('common.create'), route('course-regimes.create'));
});

// Home > Course regimes
Breadcrumbs::for('course-regimes.show', function ($trail, $item) {
    $trail->parent('course-regimes');
    $trail->push($item->code, route('course-regimes.show', $item->id));
});

// Home > Course regimes > Edit
Breadcrumbs::for('course-regimes.edit', function ($trail, $item) {
    $trail->parent('course-regimes');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('course-regimes.edit', $item->id));
});


//======================================================================================================================
// GA - Degrees
//======================================================================================================================
// Home > Degrees
Breadcrumbs::for('degrees', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.degrees'), route('degrees.index'));
});

// Home > Degrees > Create
Breadcrumbs::for('degrees.create', function ($trail) {
    $trail->parent('degrees');
    $trail->push(__('common.create'), route('degrees.create'));
});

// Home > Degree
Breadcrumbs::for('degrees.show', function ($trail, $item) {
    $trail->parent('degrees');
    $trail->push($item->code, route('degrees.show', $item->id));
});

// Home > Degrees > Edit
Breadcrumbs::for('degrees.edit', function ($trail, $item) {
    $trail->parent('degrees');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('degrees.edit', $item->id));
});


//======================================================================================================================
// GA - Duration types
//======================================================================================================================
// Home > Duration types
Breadcrumbs::for('duration-types', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.duration_types'), route('duration-types.index'));
});

// Home > Duration types > Create
Breadcrumbs::for('duration-types.create', function ($trail) {
    $trail->parent('duration-types');
    $trail->push(__('common.create'), route('duration-types.create'));
});

// Home > Duration types
Breadcrumbs::for('duration-types.show', function ($trail, $item) {
    $trail->parent('duration-types');
    $trail->push($item->code, route('duration-types.show', $item->id));
});

// Home > Duration types > Edit
Breadcrumbs::for('duration-types.edit', function ($trail, $item) {
    $trail->parent('duration-types');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('duration-types.edit', $item->id));
});

//======================================================================================================================
// GA - Courses
//======================================================================================================================
// Home > Courses
Breadcrumbs::for('courses', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.courses'), route('courses.index'));
});

// Home > Courses > Create
Breadcrumbs::for('courses.create', function ($trail) {
    $trail->parent('courses');
    $trail->push(__('common.create'), route('courses.create'));
});

// Home > Courses
Breadcrumbs::for('courses.show', function ($trail, $item) {
    $trail->parent('courses');
    $trail->push($item->code, route('courses.show', $item->id));
});

// Home > Courses > Edit
Breadcrumbs::for('courses.edit', function ($trail, $item) {
    $trail->parent('courses');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('courses.edit', $item->id));
});

//======================================================================================================================
// Year transition rules
//======================================================================================================================

// Home > Average calculation rule
Breadcrumbs::for('average-calculation-rules', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.optional_groups'), route('average-calculation-rules.index'));
});

// Home > Average calculation rule > Create
Breadcrumbs::for('average-calculation-rules.create', function ($trail) {
    $trail->parent('average-calculation-rules');
    $trail->push(__('common.create'), route('average-calculation-rules.create'));
});

// Home > Average calculation rule > [Average calculation rule]
Breadcrumbs::for('average-calculation-rules.show', function ($trail, $item) {
    $trail->parent('average-calculation-rules');
    $trail->push($item->code, route('average-calculation-rules.show', $item->id));
});

// Home > Average calculation rule > Edit - [Average calculation rule]
Breadcrumbs::for('average-calculation-rules.edit', function ($trail, $item) {
    $trail->parent('average-calculation-rules');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('average-calculation-rules.edit', $item->id));
});

//======================================================================================================================
// Year transition rule
//======================================================================================================================

// Home > Year transition rule
Breadcrumbs::for('year-transition-rules', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.optional_groups'), route('year-transition-rules.index'));
});

// Home > Year trasition rule > Create
Breadcrumbs::for('year-transition-rules.create', function ($trail) {
    $trail->parent('year-transition-rules');
    $trail->push(__('common.create'), route('year-transition-rules.create'));
});

// Home > Year transition rule > [Year transition rule]
Breadcrumbs::for('year-transition-rules.show', function ($trail, $item) {
    $trail->parent('year-transition-rules');
    $trail->push($item->code, route('year-transition-rules.show', $item->id));
});

// Home > Year transition rule > Edit - [Year transition rule]
Breadcrumbs::for('year-transition-rules.edit', function ($trail, $item) {
    $trail->parent('year-transition-rules');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('year-transition-rules.edit', $item->id));
});

//======================================================================================================================
// Lective years
//======================================================================================================================

// Home > Lective years
Breadcrumbs::for('lective-years', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.optional_groups'), route('lective-years.index'));
});

// Home > Lective years > Create
Breadcrumbs::for('lective-years.create', function ($trail) {
    $trail->parent('lective-years');
    $trail->push(__('common.create'), route('lective-years.create'));
});

// Home > Lective years > [Lective year]
Breadcrumbs::for('lective-years.show', function ($trail, $item) {
    $trail->parent('lective-years');
    $trail->push($item->code, route('lective-years.show', $item->id));
});

// Home > Lective years > Edit - [Lective year]
Breadcrumbs::for('lective-years.edit', function ($trail, $item) {
    $trail->parent('lective-years');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('lective-years.edit', $item->id));
});

//======================================================================================================================
// Period types
//======================================================================================================================

// Home > Period types
Breadcrumbs::for('period-types', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.optional_groups'), route('period-types.index'));
});

// Home > Period types > Create
Breadcrumbs::for('period-types.create', function ($trail) {
    $trail->parent('period-types');
    $trail->push(__('common.create'), route('period-types.create'));
});

// Home > Period types > [Period type]
Breadcrumbs::for('period-types.show', function ($trail, $item) {
    $trail->parent('period-types');
    $trail->push($item->code, route('period-types.show', $item->id));
});

// Home > Period types > Edit - [Period type]
Breadcrumbs::for('period-types.edit', function ($trail, $item) {
    $trail->parent('period-types');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('period-types.edit', $item->id));
});

//======================================================================================================================
// Study plan editions
//======================================================================================================================

// Home > Study plan editions
Breadcrumbs::for('study-plan-editions', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.study_plan_editions'), route('study-plan-editions.index'));
});

// Home > Study plan editions > Create
Breadcrumbs::for('study-plan-editions.create', function ($trail) {
    $trail->parent('study-plan-editions');
    $trail->push(__('common.create'), route('study-plan-editions.create'));
});

// Home > Study plan editions > [Study plan edition]
Breadcrumbs::for('study-plan-editions.show', function ($trail, $item) {
    $trail->parent('study-plan-editions');
    $trail->push($item->id, route('study-plan-editions.show', $item->id));
});

// Home > Study plan editions > Edit - [Study plan edition]
Breadcrumbs::for('study-plan-editions.edit', function ($trail, $item) {
    $trail->parent('study-plan-editions');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('study-plan-editions.edit', $item->id));
});


//======================================================================================================================
// GA - Study plans
//======================================================================================================================
// Home > Study plans
Breadcrumbs::for('study-plans', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.study_plans'), route('study-plans.index'));
});

// Home > Study plans > Create
Breadcrumbs::for('study-plans.create', function ($trail) {
    $trail->parent('study-plans');
    $trail->push(__('common.create'), route('study-plans.create'));
});

// Home > Study plans > [Study plan]
Breadcrumbs::for('study-plans.show', function ($trail, $item) {
    $trail->parent('study-plans');
    $trail->push($item->id, route('study-plans.show', $item->id));
});

// Home > Study plans > Edit - [Study plan]
Breadcrumbs::for('study-plans.edit', function ($trail, $item) {
    $trail->parent('study-plans');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('study-plans.edit', $item->id));
});

//======================================================================================================================
// GA - Access types
//======================================================================================================================
// Home > Access types
Breadcrumbs::for('access-types', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.access_types'), route('access-types.index'));
});

// Home > Access types > Create
Breadcrumbs::for('access-types.create', function ($trail) {
    $trail->parent('access-types');
    $trail->push(__('common.create'), route('access-types.create'));
});

// Home > Access types > [Access Type]
Breadcrumbs::for('access-types.show', function ($trail, $item) {
    $trail->parent('access-types');
    $trail->push($item->id, route('access-types.show', $item->id));
});

// Home > Access types > Edit - [Access Type]
Breadcrumbs::for('access-types.edit', function ($trail, $item) {
    $trail->parent('access-types');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('access-types.edit', $item->id));
});

//======================================================================================================================
// GA - Classes
//======================================================================================================================
// Home > Classes
Breadcrumbs::for('classes', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.classes'), route('classes.index'));
});

// Home > Classes > Create
Breadcrumbs::for('classes.create', function ($trail) {
    $trail->parent('classes');
    $trail->push(__('common.create'), route('classes.create'));
});

// Home > Classes > [Classes]
Breadcrumbs::for('classes.show', function ($trail, $item) {
    $trail->parent('classes');
    $trail->push($item->id, route('classes.show', $item->id));
});

// Home > Classes > Edit - [Classes]
Breadcrumbs::for('classes.edit', function ($trail, $item) {
    $trail->parent('classes');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('classes.edit', $item->id));
});

//======================================================================================================================
// GA - Discipline Classes
//======================================================================================================================
// Home > Classes
Breadcrumbs::for('discipline-classes', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline-classes'), route('discipline-classes.index'));
});

// Home > Classes > Create
Breadcrumbs::for('discipline-classes.create', function ($trail) {
    $trail->parent('discipline-classes');
    $trail->push(__('common.create'), route('discipline-classes.create'));
});

// Home > Classes > [Classes]
Breadcrumbs::for('discipline-classes.show', function ($trail, $item) {
    $trail->parent('discipline-classes');
    $trail->push($item->id, route('discipline-classes.show', $item->id));
});

// Home > Classes > Edit - [Classes]
Breadcrumbs::for('discipline-classes.edit', function ($trail, $item) {
    $trail->parent('discipline-classes');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('discipline-classes.edit', $item->id));
});

//======================================================================================================================
// GA - Discipline Curricula
//======================================================================================================================
// Home > Discipline Curricula
Breadcrumbs::for('discipline-curricula', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline_curricula'), route('discipline-curricula.index'));
});

// Home > Discipline Curricula > Create
Breadcrumbs::for('discipline-curricula.create', function ($trail) {
    $trail->parent('discipline-curricula');
    $trail->push(__('common.create'), route('discipline-curricula.create'));
});

// Home > Discipline Curricula > [Curricula]
Breadcrumbs::for('discipline-curricula.show', function ($trail, $item) {
    $trail->parent('discipline-curricula');
    $trail->push($item->id, route('discipline-curricula.show', $item->id));
});

// Home > Discipline Curricula > Edit - [Curricula]
Breadcrumbs::for('discipline-curricula.edit', function ($trail, $item) {
    $trail->parent('discipline-curricula');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('discipline-curricula.edit', $item->id));
});


//======================================================================================================================
// GA - Discipline Absence Configuration
//======================================================================================================================
// Home > Discipline Absence Configuration
Breadcrumbs::for('discipline-absence-configuration', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.discipline_absence_configuration'), route('discipline-absence-configuration.index'));
});

// Home > Discipline Absence Configuration > Create
Breadcrumbs::for('discipline-absence-configuration.create', function ($trail) {
    $trail->parent('discipline-absence-configuration');
    $trail->push(__('common.create'), route('discipline-absence-configuration.create'));
});

// Home > Discipline Absence Configuration > [Configuration]
Breadcrumbs::for('discipline-absence-configuration.show', function ($trail, $item) {
    $trail->parent('discipline-absence-configuration');
    $trail->push($item->id, route('discipline-absence-configuration.show', $item->id));
});

// Home > Study Plan Edition > Absences > Permissions
Breadcrumbs::for('discipline-absence-configuration.edit', function ($trail, $item) {
    $trail->parent('study-plan-editions');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('discipline-absence-configuration.edit', $item->id));
});

//======================================================================================================================
// Menus
//======================================================================================================================
// Home > Menus
Breadcrumbs::for('menus', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.menus'), route('menus.index'));
});

// Home > Menus > Create
Breadcrumbs::for('menus.create', function ($trail) {
    $trail->parent('menus');
    $trail->push(__('common.create'), route('menus.create'));
});

// Home > Menus > [Menu]
Breadcrumbs::for('menus.show', function ($trail, $item) {
    $trail->parent('menus');
    $trail->push($item->id, route('menus.show', $item->id));
});

// Home > Menus > Edit - [Menu]
Breadcrumbs::for('menus.edit', function ($trail, $item) {
    $trail->parent('menus');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('menus.edit', $item->id));
});

//======================================================================================================================
// Menu Items
//======================================================================================================================
// Home > Menus Items
Breadcrumbs::for('menu-items', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.menu_items'), route('menu-items.index'));
});

// Home > Menu Items > Create
Breadcrumbs::for('menu-items.create', function ($trail) {
    $trail->parent('menu-items');
    $trail->push(__('common.create'), route('menu-items.create'));
});

// Home > Menu Items > [Menu]
Breadcrumbs::for('menu-items.show', function ($trail, $item) {
    $trail->parent('menu-items');
    $trail->push($item->code, route('menu-items.show', $item->id));
});

// Home > Menu Items > Edit - [Menu]
Breadcrumbs::for('menu-items.edit', function ($trail, $item) {
    $trail->parent('menu-items');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('menu-items.edit', $item->id));
});

//======================================================================================================================
// Enrollment State Types
//======================================================================================================================
// Home > Enrollment State Types
Breadcrumbs::for('enrollment-state-types', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.enrollment_state_types'), route('enrollment-state-types.index'));
});

// Home > Enrollment State Types > Create
Breadcrumbs::for('enrollment-state-types.create', function ($trail) {
    $trail->parent('enrollment-state-types');
    $trail->push(__('common.create'), route('enrollment-state-types.create'));
});

// Home > Enrollment State Types > [Menu]
Breadcrumbs::for('enrollment-state-types.show', function ($trail, $item) {
    $trail->parent('enrollment-state-types');
    $trail->push($item->id, route('enrollment-state-types.show', $item->id));
});

// Home > Enrollment State Types > Edit - [Menu]
Breadcrumbs::for('enrollment-state-types.edit', function ($trail, $item) {
    $trail->parent('enrollment-state-types');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('enrollment-state-types.edit', $item->id));
});

//======================================================================================================================
// Enrollments
//======================================================================================================================
// Home > Enrollments
Breadcrumbs::for('enrollments', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.enrollments'), route('enrollments.index'));
});

// Home > Enrollments > Create
Breadcrumbs::for('enrollments.create', function ($trail) {
    $trail->parent('enrollments');
    $trail->push(__('common.create'), route('enrollments.create'));
});

// Home > Enrollments > [Menu]
Breadcrumbs::for('enrollments.show', function ($trail, $item) {
    $trail->parent('enrollments');
    $trail->push($item->id, route('enrollments.show', $item->id)); //FIXME: Title
});

// Home > Enrollments > Edit - [Menu]
Breadcrumbs::for('enrollments.edit', function ($trail, $item) {
    $trail->parent('enrollments');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('enrollments.edit', $item->id));
});

//======================================================================================================================
// Languages
//======================================================================================================================
// Home > Languages
Breadcrumbs::for('languages', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.languages'), route('languages.index'));
});

// Home > Languages > Create
Breadcrumbs::for('languages.create', function ($trail) {
    $trail->parent('languages');
    $trail->push(__('common.create'), route('languages.create'));
});

// Home > Languages > [Menu]
Breadcrumbs::for('languages.show', function ($trail, $item) {
    $trail->parent('languages');
    $trail->push($item->name, route('languages.show', $item->id)); //FIXME: Title
});

// Home > Languages > Edit - [Menu]
Breadcrumbs::for('languages.edit', function ($trail, $item) {
    $trail->parent('languages');
    $trail->push(__('common.edit') . ' - ' . $item->name, route('languages.edit', $item->id));
});

//======================================================================================================================
// Events
//======================================================================================================================
// Home > Events
Breadcrumbs::for('events', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.events'), route('events.index'));
});

// Home > Events > Create
Breadcrumbs::for('events.create', function ($trail) {
    $trail->parent('events');
    $trail->push(__('common.create'), route('events.create'));
});

// Home > Events > [Item]
Breadcrumbs::for('events.show', function ($trail, $item) {
    $trail->parent('events');
    $trail->push($item->id, route('events.show', $item->id)); //FIXME: Title
});

// Home > Events > Edit - [Item]
Breadcrumbs::for('events.edit', function ($trail, $item) {
    $trail->parent('events');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('events.edit', $item->id));
});

//======================================================================================================================
// EventType
//======================================================================================================================
// Home > EventType
Breadcrumbs::for('event-types', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.event-types'), route('event-types.index'));
});

// Home > EventType > Create
Breadcrumbs::for('event-types.create', function ($trail) {
    $trail->parent('event-types');
    $trail->push(__('common.create'), route('event-types.create'));
});

// Home > EventType > [Item]
Breadcrumbs::for('event-types.show', function ($trail, $item) {
    $trail->parent('event-types');
    $trail->push($item->code, route('event-types.show', $item->id));
});

// Home > EventType > Edit - [Item]
Breadcrumbs::for('event-types.edit', function ($trail, $item) {
    $trail->parent('event-types');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('event-types.edit', $item->id));
});

//======================================================================================================================
// Summaries
//======================================================================================================================
// Home > Summaries
Breadcrumbs::for('summaries', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.summaries'), route('summaries.index'));
});

// Home > Summaries > Create
Breadcrumbs::for('summaries.create', function ($trail) {
    $trail->parent('summaries');
    $trail->push(__('common.create'), route('summaries.create'));
});

// Home > Summaries > [Item]
Breadcrumbs::for('summaries.show', function ($trail, $item) {
    $trail->parent('summaries');
    $trail->push($item->id, route('summaries.show', $item->id));
});

// Home > Summaries > Edit - [Item]
Breadcrumbs::for('summaries.edit', function ($trail, $item) {
    $trail->parent('summaries');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('summaries.edit', $item->id));
});

//======================================================================================================================
// Days of the Week
//======================================================================================================================
// Home > Days of the Week
Breadcrumbs::for('days-of-the-week', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.days-of-the-week'), route('days-of-the-week.index'));
});

// Home > Days of the Week > Create
Breadcrumbs::for('days-of-the-week.create', function ($trail) {
    $trail->parent('days-of-the-week');
    $trail->push(__('common.create'), route('days-of-the-week.create'));
});

// Home > Days of the Week > [Item]
Breadcrumbs::for('days-of-the-week.show', function ($trail, $item) {
    $trail->parent('days-of-the-week');
    $trail->push($item->id, route('days-of-the-week.show', $item->id));
});

// Home > Days of the Week > Edit - [Item]
Breadcrumbs::for('days-of-the-week.edit', function ($trail, $item) {
    $trail->parent('days-of-the-week');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('days-of-the-week.edit', $item->id));
});

//======================================================================================================================
// Buildings
//======================================================================================================================
// Home > Buildings
Breadcrumbs::for('buildings', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.buildings'), route('buildings.index'));
});

// Home > Buildings > Create
Breadcrumbs::for('buildings.create', function ($trail) {
    $trail->parent('buildings');
    $trail->push(__('common.create'), route('buildings.create'));
});

// Home > Buildings > [Item]
Breadcrumbs::for('buildings.show', function ($trail, $item) {
    $trail->parent('buildings');
    $trail->push($item->code, route('buildings.show', $item->id));
});

// Home > Buildings > Edit - [Item]
Breadcrumbs::for('buildings.edit', function ($trail, $item) {
    $trail->parent('buildings');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('buildings.edit', $item->id));
});

//======================================================================================================================
// Rooms
//======================================================================================================================
// Home > Rooms
Breadcrumbs::for('rooms', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.rooms'), route('rooms.index'));
});

// Home > Rooms > Create
Breadcrumbs::for('rooms.create', function ($trail) {
    $trail->parent('rooms');
    $trail->push(__('common.create'), route('rooms.create'));
});

// Home > Rooms > [Item]
Breadcrumbs::for('rooms.show', function ($trail, $item) {
    $trail->parent('rooms');
    $trail->push($item->code, route('rooms.show', $item->id));
});

// Home > Rooms > Edit - [Item]
Breadcrumbs::for('rooms.edit', function ($trail, $item) {
    $trail->parent('rooms');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('rooms.edit', $item->id));
});

//======================================================================================================================
// Schedule Types
//======================================================================================================================
// Home > Schedule Types
Breadcrumbs::for('schedule-types', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.schedule-types'), route('schedule-types.index'));
});

// Home > Schedule Types > Create
Breadcrumbs::for('schedule-types.create', function ($trail) {
    $trail->parent('schedule-types');
    $trail->push(__('common.create'), route('schedule-types.create'));
});

// Home > Schedule Types > [Item]
Breadcrumbs::for('schedule-types.show', function ($trail, $item) {
    $trail->parent('schedule-types');
    $trail->push($item->code, route('schedule-types.show', $item->id));
});

// Home > Schedule Types > Edit - [Item]
Breadcrumbs::for('schedule-types.edit', function ($trail, $item) {
    $trail->parent('schedule-types');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('schedule-types.edit', $item->id));
});

//======================================================================================================================
// Schedules
//======================================================================================================================
// Home > Schedule Types
Breadcrumbs::for('schedules', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.schedules'), route('schedules.index'));
});

// Home > Schedule Types > Create
Breadcrumbs::for('schedules.create', function ($trail) {
    $trail->parent('schedules');
    $trail->push(__('common.create'), route('schedules.create'));
});

// Home > Schedule Types > [Item]
Breadcrumbs::for('schedules.show', function ($trail, $item) {
    $trail->parent('schedules');
    $trail->push($item->code, route('schedules.show', $item->id));
});

// Home > Schedule Types > Edit - [Item]
Breadcrumbs::for('schedules.edit', function ($trail, $item) {
    $trail->parent('schedules');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('schedules.edit', $item->id));
});

//======================================================================================================================
// Payments
//======================================================================================================================

// Home > Payments
Breadcrumbs::for('payments', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.payments'), route('requests.index'));
});

// Home > Payments > Create
Breadcrumbs::for('payments.create', function ($trail) {
    $trail->parent('payments');
    $trail->push(__('common.create'), route('requests.create'));
});

// Home > Payments > [Request]
Breadcrumbs::for('payments.show', function ($trail, $item) {
    $trail->parent('payments');
    $trail->push(__('Payments::payments.payment'), route('requests.show', $item->id));
});

//======================================================================================================================
// Articles
//======================================================================================================================

// Home > Articles
Breadcrumbs::for('articles', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.articles'), route('articles.index'));
});

// Home > Articles > Create
Breadcrumbs::for('articles.create', function ($trail) {
    $trail->parent('articles');
    $trail->push(__('common.create'), route('articles.create'));
});

// Home > Articles > [Article]
Breadcrumbs::for('articles.show', function ($trail, $item) {
    $trail->parent('articles');
    $trail->push($item->code, route('articles.show', $item->id));
});

// Home > Articles > Edit - [Article]
Breadcrumbs::for('articles.edit', function ($trail, $item) {
    $trail->parent('articles');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('articles.edit', $item->id));
});

//======================================================================================================================
// Banks
//======================================================================================================================

// Home > Banks
Breadcrumbs::for('banks', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.banks'), route('banks.index'));
});

// Home > Banks > Create
Breadcrumbs::for('banks.create', function ($trail) {
    $trail->parent('banks');
    $trail->push(__('common.create'), route('banks.create'));
});

// Home > Banks > [Bank]
Breadcrumbs::for('banks.show', function ($trail, $item) {
    $trail->parent('banks');
    $trail->push($item->code, route('banks.show', $item->id));
});

// Home > Banks > Edit - [Bank]
Breadcrumbs::for('banks.edit', function ($trail, $item) {
    $trail->parent('banks');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('banks.edit', $item->id));
});

//======================================================================================================================
// ArticleRequests
//======================================================================================================================

// Home > ArticleRequests
Breadcrumbs::for('requests', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.requests'), route('requests.index'));
});

// Home > ArticleRequests > Create
Breadcrumbs::for('requests.create', function ($trail) {
    $trail->parent('requests');
    $trail->push(__('common.create'), route('requests.create'));
});

// Home > ArticleRequests > [ArticleRequest]
Breadcrumbs::for('requests.show', function ($trail, $item) {
    $trail->parent('requests');
    $trail->push($item->id, route('requests.show', $item->id));
});

// Home > ArticleRequests > Edit - [ArticleRequest]
Breadcrumbs::for('requests.edit', function ($trail, $item) {
    $trail->parent('requests');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('requests.edit', $item->id));
});

// Home > ArticleRequests > Transaction
Breadcrumbs::for('requests.transaction', function ($trail) {
    $trail->parent('requests');
     $trail->push(__('header.transaction')/*, route('transaction-request.index')*/);
});

// Home > ArticleRequests > Transaction > Create
Breadcrumbs::for('requests.transaction.create', function ($trail) {
    $trail->parent('requests.transaction');
     $trail->push(__('common.create')/*, route('transaction-request.create')*/);
});

//======================================================================================================================
// Transactions
//======================================================================================================================

// Home > Transactions
Breadcrumbs::for('transactions', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.transactions'), route('transactions.index'));
});

// Home > Transactions > Create
Breadcrumbs::for('transactions.create', function ($trail) {
    $trail->parent('transactions');
    $trail->push(__('common.create'), route('transactions.create'));
});

// Home > Transactions > [Transaction]
Breadcrumbs::for('transactions.show', function ($trail, $item) {
    $trail->parent('transactions');
    $trail->push($item->code, route('transactions.show', $item->id));
});

// Home > Transactions > Edit - [Transaction]
Breadcrumbs::for('transactions.edit', function ($trail, $item) {
    $trail->parent('transactions');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('transactions.edit', $item->id));
});

//======================================================================================================================
// Grade
//======================================================================================================================

// Home > Grade
Breadcrumbs::for('grades', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.give_grades'), route('grade_teacher.index'));
});

Breadcrumbs::for('grades.student', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.grades'), route('grade_student.index'));
});

//======================================================================================================================
// Matriculations
//======================================================================================================================

// Home > Matriculations
Breadcrumbs::for('matriculations', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.matriculations'), route('matriculations.index'));
});

// Home > Matriculations > Create
Breadcrumbs::for('matriculations.create', function ($trail) {
    $trail->parent('matriculations');
    $trail->push(__('common.create'), route('matriculations.create'));
});

// Home > Matriculations > [Matriculation]
Breadcrumbs::for('matriculations.show', function ($trail, $item) {
    $trail->parent('matriculations');
    $trail->push($item->code, route('matriculations.show', $item->id));
});

// Home > Matriculations > Edit - [Matriculation]
Breadcrumbs::for('matriculations.edit', function ($trail, $item) {
    $trail->parent('matriculations');
    $trail->push(__('common.edit') . ' - ' . $item->code, route('matriculations.edit', $item->id));
});

//======================================================================================================================
// Lessons
//======================================================================================================================

// Home > Lessons
Breadcrumbs::for('lessons', function ($trail) {
    $trail->parent('home');
    $trail->push(__('header.lessons'), route('lessons.index'));
});

// Home > Lessons > Create
Breadcrumbs::for('lessons.create', function ($trail) {
    $trail->parent('lessons');
    $trail->push(__('common.create'), route('lessons.create'));
});

// Home > Lessons > [Lesson]
Breadcrumbs::for('lessons.show', function ($trail, $item) {
    $trail->parent('lessons');
    $trail->push($item->id, route('lessons.show', $item->id));
});

// Home > Lessons > Edit - [Lesson]
Breadcrumbs::for('lessons.edit', function ($trail, $item) {
    $trail->parent('lessons');
    $trail->push(__('common.edit') . ' - ' . $item->id, route('lessons.edit', $item->id));
});
