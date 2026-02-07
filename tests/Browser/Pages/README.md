# Dusk Page Objects

This directory contains Page objects for Laravel Dusk tests. Page objects are a design pattern that allows you to encapsulate the structure of a page in a single place, making your tests more maintainable and readable.

## Available Page Objects

### Base Page Object

- `Page.php`: The base Page object that all other Page objects extend. It defines global element shortcuts that can be used across all pages.

### Public Pages

- `HomePage.php`: Represents the home page of the application.

### Authentication Pages

- `LoginPage.php`: Represents the login page.
- `RegisterPage.php`: Represents the registration page.
- `ForgotPasswordPage.php`: Represents the forgot password page.

### Dashboard Pages

- `DashboardPage.php`: Represents the main dashboard page.

### Settings Pages

- `ProfileSettingsPage.php`: Represents the profile settings page.
- `PasswordSettingsPage.php`: Represents the password settings page.
- `AppearanceSettingsPage.php`: Represents the appearance settings page.

## Using Page Objects

Page objects can be used in Dusk tests to interact with pages in a more maintainable way. Here's an example of how to use a Page object in a test:

```php
use Tests\Browser\Pages\LoginPage;

test('user can log in', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new LoginPage)
                ->type('@email', 'test@example.com')
                ->type('@password', 'password')
                ->click('@login-button')
                ->assertPathIs('/dashboard');
    });
});
```

Or using the helper methods provided by the Page object:

```php
use Tests\Browser\Pages\LoginPage;

test('user can log in', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new LoginPage)
                ->login('test@example.com', 'password')
                ->assertPathIs('/dashboard');
    });
});
```

## Global Element Shortcuts

The base `Page` class defines global element shortcuts that can be used across all pages:

- `@sidebar`: The sidebar component.
- `@header`: The header component.
- `@footer`: The footer component.
- `@logo`: The logo.
- `@user-dropdown`: The user dropdown menu.
- `@dashboard-link`: The dashboard link.
- `@pages-link`: The pages link.
- `@settings-link`: The settings link.
- `@logout-button`: The logout button.
- `@toast`: The toast notification component.

## Best Practices

1. **Keep Page objects focused**: Each Page object should represent a single page or component.
2. **Use element shortcuts**: Define element shortcuts for all elements that you interact with in tests.
3. **Add helper methods**: Add helper methods for common interactions with the page.
4. **Keep assertions minimal**: Page objects should only assert that they are on the correct page, not test functionality.
5. **Update Page objects when the UI changes**: Keep Page objects in sync with UI changes to prevent test failures.
