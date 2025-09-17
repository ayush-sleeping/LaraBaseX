// resources/js/pages/frontend/contact.tsx

import { Navbar } from '@/components/Frontend/navbar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

// Contact Form Component with Inertia.js form handling
const ContactForm = () => {
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        mobile: '',
        subject: '',
        message: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post('/contact/store', {
            // Remove preserveScroll to allow redirect to work
            onSuccess: () => {
                // Don't reset form here - let the redirect handle navigation
                console.log('Form submitted successfully - should redirect now');
            },
            onError: (errors) => {
                console.error('Validation errors:', errors);
            },
        });
    };

    return (
        <div className="mx-auto flex max-w-screen-md flex-col gap-6 rounded-lg border p-10">
            <form onSubmit={submit} className="flex flex-col gap-6">
                {/* First Name & Last Name */}
                <div className="flex gap-4">
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="first_name">First Name</Label>
                        <Input
                            type="text"
                            id="first_name"
                            name="first_name"
                            placeholder="First Name"
                            value={data.first_name}
                            onChange={(e) => setData('first_name', e.target.value)}
                            required
                            className={errors.first_name ? 'border-red-500' : ''}
                        />
                        {errors.first_name && <span className="text-sm text-red-500">{errors.first_name}</span>}
                    </div>
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="last_name">Last Name</Label>
                        <Input
                            type="text"
                            id="last_name"
                            name="last_name"
                            placeholder="Last Name"
                            value={data.last_name}
                            onChange={(e) => setData('last_name', e.target.value)}
                            required
                            className={errors.last_name ? 'border-red-500' : ''}
                        />
                        {errors.last_name && <span className="text-sm text-red-500">{errors.last_name}</span>}
                    </div>
                </div>
                {/* Email & Mobile */}
                <div className="flex gap-4">
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            required
                            className={errors.email ? 'border-red-500' : ''}
                        />
                        {errors.email && <span className="text-sm text-red-500">{errors.email}</span>}
                    </div>
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="mobile">Mobile</Label>
                        <Input
                            type="tel"
                            id="mobile"
                            name="mobile"
                            placeholder="Mobile"
                            value={data.mobile}
                            onChange={(e) => setData('mobile', e.target.value)}
                            required
                            className={errors.mobile ? 'border-red-500' : ''}
                        />
                        {errors.mobile && <span className="text-sm text-red-500">{errors.mobile}</span>}
                    </div>
                </div>
                {/* Subject */}
                <div className="grid w-full items-center gap-1.5">
                    <Label htmlFor="subject">Subject</Label>
                    <Input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="Subject"
                        value={data.subject}
                        onChange={(e) => setData('subject', e.target.value)}
                        required
                        className={errors.subject ? 'border-red-500' : ''}
                    />
                    {errors.subject && <span className="text-sm text-red-500">{errors.subject}</span>}
                </div>
                {/* Message */}
                <div className="grid w-full gap-1.5">
                    <Label htmlFor="message">Message</Label>
                    <Textarea
                        placeholder="Type your message here."
                        id="message"
                        name="message"
                        rows={4}
                        value={data.message}
                        onChange={(e) => setData('message', e.target.value)}
                        required
                        className={errors.message ? 'border-red-500' : ''}
                    />
                    {errors.message && <span className="text-sm text-red-500">{errors.message}</span>}
                </div>
                {/* Submit Button */}
                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? 'Sending...' : 'Send Message'}
                </Button>
            </form>
        </div>
    );
};

interface ContactProps {
    title?: string;
    description?: string;
}

export const Contact = ({
    title = 'Contact Us',
    description = 'We are available for questions, feedback, or collaboration opportunities. Let us know how we can help!',
}: ContactProps) => {
    return (
        <section className="py-32">
            <div className="container">
                <div className="mx-auto flex max-w-screen-xl flex-col justify-between gap-10 lg:flex-row lg:gap-20">
                    <div className="mx-auto flex max-w-sm flex-col justify-between gap-10">
                        <div className="text-center lg:text-left">
                            <h1 className="mb-2 text-5xl font-semibold lg:mb-1 lg:text-6xl">{title}</h1>
                            <p className="text-muted-foreground">{description}</p>
                        </div>
                        <div className="mx-auto w-fit lg:mx-0">
                            <h3 className="mb-6 text-center text-2xl font-semibold lg:text-left">How to Get Help</h3>
                            <ul className="ml-4 list-disc space-y-2">
                                <li>
                                    <span className="font-bold">🐛 Found a Bug? </span>
                                    <a
                                        href="https://github.com/ayush-sleeping/LaraBaseX/issues/new"
                                        target="_blank"
                                        className="text-blue-600 underline"
                                    >
                                        Create an Issue on GitHub
                                    </a>
                                </li>
                                <li>
                                    <span className="font-bold">💡 Feature Request? </span>
                                    <a
                                        href="https://github.com/ayush-sleeping/LaraBaseX/discussions"
                                        target="_blank"
                                        className="text-blue-600 underline"
                                    >
                                        Start a Discussion
                                    </a>
                                </li>
                                <li>
                                    <span className="font-bold">📖 Documentation: </span>
                                    <a
                                        href="https://github.com/ayush-sleeping/LaraBaseX/tree/main/documentation"
                                        target="_blank"
                                        className="text-blue-600 underline"
                                    >
                                        View Docs
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    {/* Contact Form */}
                    <ContactForm />
                </div>
            </div>
        </section>
    );
};

export default function ContactPage() {
    const laraBaseXContactProps = {
        title: 'Get Support',
        description: "Need help with LaraBaseX? Found a bug or have suggestions? We're here to help you get the most out of your starter kit.",
    };

    return (
        <div className="relative min-h-screen w-full bg-white">
            <Navbar />
            <Contact {...laraBaseXContactProps} />
        </div>
    );
}
