# WorkflowStatus Component Dusk Tests

This directory contains Dusk tests for the WorkflowStatus component. The tests verify that the component renders correctly and responds to user interactions as expected.

## Test Coverage

The tests cover the following aspects of the WorkflowStatus component:

1. **Visibility Test**: Verifies that the WorkflowStatus component is visible on the dashboard and displays the traffic light indicators.
2. **Initial State Test**: Checks that the component shows the correct initial state (Content Format: Markdown, Conversion: Idle, Status: Loading).
3. **Page Manager Interaction Test**: Tests that the workflow status updates when opening the page manager.
4. **Error Message Test**: Verifies that the component can display error messages.
5. **RadialChart Test**: Checks that the RadialChart is displayed in the workflow status component.

## Authentication

All tests include authentication steps since the dashboard is protected by authentication middleware. Each test:

1. Creates a test user using the User factory
2. Visits the login page
3. Submits the login form with the test user's credentials
4. Verifies successful login by checking the redirect to the dashboard
5. Proceeds with the test-specific assertions

## Running the Tests

To run the tests, use the following command:

```bash
php artisan dusk tests/Browser/Components/WorkflowStatusTest.php
```

Make sure that the ChromeDriver is installed and running before executing the tests. You can install the ChromeDriver using:

```bash
php artisan dusk:chrome-driver
```

Or, to install all ChromeDriver binaries:

```bash
php artisan dusk:chrome-driver --all
```

## Troubleshooting

If you encounter issues with the ChromeDriver connection, try the following:

1. Make sure the ChromeDriver is running on port 9515.
2. Check that the ChromeDriver version matches your Chrome browser version.
3. Try running the tests with the `--without-tty` flag:

```bash
php artisan dusk:chrome-driver --detect
php artisan dusk tests/Browser/Components/WorkflowStatusTest.php --without-tty
```

## Test Implementation Details

The tests use the following selectors to interact with the WorkflowStatus component:

- `[data-slot="card"]`: The Card component that contains the WorkflowStatus.
- `.rounded-full`: The traffic light indicators for workflow stage and conversion status.
- `button span:contains("Pages")`: The Pages button in the sidebar that opens the page manager.
- `[data-slot="sheet-content"]`: The content of the page manager sheet.
- `.bg-yellow-500` and `.bg-blue-500`: The traffic light colors for different workflow stages.
- `[data-slot="alert"]`: The container for error messages.
- `svg` and `.recharts-responsive-container`: The RadialChart component.

These selectors are based on the implementation of the WorkflowStatus component and may need to be updated if the component's structure changes.
