// resources/js/pages/frontend/contact/thankYou.tsx

import { Navbar } from '@/components/Frontend/navbar';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, Home } from 'lucide-react';

interface ThankYouProps {
    title?: string;
    description?: string;
    mainImage?: {
        src: string;
        alt: string;
    };
    secondaryImage?: {
        src: string;
        alt: string;
    };
    breakout?: {
        src: string;
        alt: string;
        title?: string;
        description?: string;
        buttonText?: string;
        buttonUrl?: string;
    };
    nextStepsTitle?: string;
    nextSteps?: Array<{
        label: string;
        description: string;
        icon?: string;
    }>;
}

export const ThankYouSection = ({
    title = 'Thank You!',
    description = 'Your message has been sent successfully. We appreciate you reaching out to us and will get back to you as soon as possible.',
    mainImage = {
        src: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        alt: 'Success celebration',
    },
    secondaryImage = {
        src: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        alt: 'Team working together',
    },
    breakout = {
        src: '/logo.svg',
        alt: 'LaraBaseX Logo',
        title: 'Get Started with LaraBaseX',
        description: 'While you wait for our response, explore the comprehensive documentation and start building amazing applications.',
        buttonText: 'View Documentation',
        buttonUrl: 'https://github.com/ayush-sleeping/LaraBaseX/tree/main/documentation',
    },
    nextStepsTitle = 'What happens next?',
    nextSteps = [
        {
            label: 'Response Time',
            description: 'We typically respond within 24-48 hours during business days.',
            icon: '⏰',
        },
        {
            label: 'GitHub Issues',
            description: 'For technical issues, consider creating a GitHub issue for faster resolution.',
            icon: '🐛',
        },
        {
            label: 'Documentation',
            description: 'Explore our comprehensive guides while you wait for our response.',
            icon: '📚',
        },
        {
            label: 'Community',
            description: 'Join discussions and connect with other LaraBaseX developers.',
            icon: '👥',
        },
    ],
}: ThankYouProps = {}) => {
    return (
        <section className="py-32">
            <div className="container mx-auto">
                {/* Header with Success Icon */}
                <div className="mb-14 text-center">
                    <div className="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                        <CheckCircle className="h-10 w-10 text-green-600" />
                    </div>
                    <h1 className="mb-4 text-5xl font-semibold text-gray-900">{title}</h1>
                    <p className="mx-auto max-w-2xl text-lg text-muted-foreground">{description}</p>

                    {/* Action Buttons */}
                    <div className="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <Button asChild variant="default">
                            <Link href="/">
                                <Home className="mr-2 h-4 w-4" />
                                Back to Home
                            </Link>
                        </Button>
                        <Button asChild variant="outline">
                            <Link href="/contact">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Contact
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Main Content Grid */}
                <div className="grid gap-7 lg:grid-cols-3">
                    <img src={mainImage.src} alt={mainImage.alt} className="size-full max-h-[620px] rounded-xl object-cover lg:col-span-2" />
                    <div className="flex flex-col gap-7 md:flex-row lg:flex-col">
                        {/* Breakout Card */}
                        <div className="flex flex-col justify-between gap-6 rounded-xl bg-muted p-7 md:w-1/2 lg:w-auto">
                            <img src={breakout.src} alt={breakout.alt} className="mr-auto h-12" />
                            <div>
                                <p className="mb-2 text-lg font-semibold">{breakout.title}</p>
                                <p className="text-muted-foreground">{breakout.description}</p>
                            </div>
                            <Button variant="outline" className="mr-auto" asChild>
                                <a href={breakout.buttonUrl} target="_blank" rel="noopener noreferrer">
                                    {breakout.buttonText}
                                </a>
                            </Button>
                        </div>
                        <img
                            src={secondaryImage.src}
                            alt={secondaryImage.alt}
                            className="grow basis-0 rounded-xl object-cover md:w-1/2 lg:min-h-0 lg:w-auto"
                        />
                    </div>
                </div>

                {/* Next Steps Section */}
                <div className="relative mt-32 overflow-hidden rounded-xl bg-muted p-10 md:p-16">
                    <div className="mb-10 flex flex-col gap-4 text-center md:text-left">
                        <h2 className="text-4xl font-semibold">{nextStepsTitle}</h2>
                        <p className="max-w-screen-sm text-muted-foreground">
                            Here's what you can expect and what you can do while waiting for our response.
                        </p>
                    </div>
                    <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                        {nextSteps.map((step, idx) => (
                            <div className="flex flex-col gap-4 text-center" key={step.label + idx}>
                                <div className="text-4xl">{step.icon}</div>
                                <div>
                                    <p className="mb-2 font-semibold">{step.label}</p>
                                    <p className="text-sm text-muted-foreground">{step.description}</p>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Quick Links */}
                    <div className="mt-10 flex flex-wrap justify-center gap-4">
                        <Button variant="outline" size="sm" asChild>
                            <a href="https://github.com/ayush-sleeping/LaraBaseX/issues/new" target="_blank" rel="noopener noreferrer">
                                Report Issue
                            </a>
                        </Button>
                        <Button variant="outline" size="sm" asChild>
                            <Link href="/blogs">Learning Resources</Link>
                        </Button>
                        <Button variant="outline" size="sm" asChild>
                            <Link href="/services">View Features</Link>
                        </Button>
                        <Button variant="outline" size="sm" asChild>
                            <Link href="/about">About LaraBaseX</Link>
                        </Button>
                    </div>

                    {/* Background Pattern */}
                    <div className="pointer-events-none absolute -top-1 right-1 z-10 hidden h-full w-full bg-[linear-gradient(to_right,hsl(var(--muted-foreground))_1px,transparent_1px),linear-gradient(to_bottom,hsl(var(--muted-foreground))_1px,transparent_1px)] [mask-image:linear-gradient(to_bottom_right,#000,transparent,transparent)] bg-[size:80px_80px] opacity-15 md:block"></div>
                </div>
            </div>
        </section>
    );
};

export default function ThankYouPage() {
    return (
        <div className="relative min-h-screen w-full bg-white">
            <Navbar />
            <ThankYouSection />
        </div>
    );
}
