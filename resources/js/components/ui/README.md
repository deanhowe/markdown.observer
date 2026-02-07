# Form Components

This directory contains UI components for building forms with React Hook Form and Zod validation.

## Installation

The form components require the following dependencies:

```bash
npm install react-hook-form zod @hookform/resolvers
```

## Components

### Form

The `Form` component is a wrapper around React Hook Form's `FormProvider`. It provides form context to all form components.

### FormField

The `FormField` component connects form fields to React Hook Form. It uses the `Controller` component from React Hook Form to handle form state.

### FormItem

The `FormItem` component is a container for form fields. It provides context for form field components.

### FormLabel

The `FormLabel` component is a label for form fields. It shows validation errors by changing color.

### FormControl

The `FormControl` component connects form controls to the form state. It adds accessibility attributes to form controls.

### FormDescription

The `FormDescription` component displays a description for form fields.

### FormMessage

The `FormMessage` component displays validation error messages.

### FormError

The `FormError` component uses the existing `InputError` component to display validation errors.

## Usage

Here's an example of how to use the form components:

```tsx
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';

import { Button } from '@/components/ui/button';
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';

// Define the form schema with Zod
const formSchema = z.object({
    username: z.string().min(2, {
        message: 'Username must be at least 2 characters.',
    }),
    email: z.string().email({
        message: 'Please enter a valid email address.',
    }),
});

// Infer the type from the schema
type FormValues = z.infer<typeof formSchema>;

export function MyForm() {
    // Initialize the form with React Hook Form
    const form = useForm<FormValues>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            username: '',
            email: '',
        },
    });

    // Define the form submission handler
    function onSubmit(values: FormValues) {
        console.log(values);
    }

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                <FormField
                    control={form.control}
                    name="username"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Username</FormLabel>
                            <FormControl>
                                <Input placeholder="Username" {...field} />
                            </FormControl>
                            <FormDescription>
                                This is your public display name.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="email"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                                <Input placeholder="Email" {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <Button type="submit">Submit</Button>
            </form>
        </Form>
    );
}
```

## Validation

The form components use Zod for validation. Zod is a TypeScript-first schema validation library that allows you to define the shape of your data and validate it at runtime.

Here's an example of how to define a validation schema with Zod:

```tsx
const formSchema = z.object({
    username: z.string().min(2, {
        message: 'Username must be at least 2 characters.',
    }),
    email: z.string().email({
        message: 'Please enter a valid email address.',
    }),
    age: z.number().min(18, {
        message: 'You must be at least 18 years old.',
    }),
});
```

You can then use this schema with React Hook Form's `zodResolver`:

```tsx
const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
        username: '',
        email: '',
        age: 0,
    },
});
```

## Accessibility

The form components include accessibility features:

- Labels are properly associated with form controls
- Error messages are announced to screen readers
- Form controls have appropriate ARIA attributes
- Form validation errors are visually indicated

## Styling

The form components use Tailwind CSS for styling. You can customize the appearance by passing className props to the components.
