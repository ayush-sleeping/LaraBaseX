// resources/js/pages/frontend/contact.tsx
import { Navbar } from '@/components/Frontend/navbar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

export default function ContactPage() {
    return (
        <div className="relative min-h-screen w-full bg-white">
            <Navbar />

            <div className="container mx-auto px-4 py-16">
                <div className="grid grid-cols-1 items-start gap-12 lg:grid-cols-2">
                    {/* Left content */}
                    <div>
                        <h1 className="mb-4 text-4xl font-bold">Contact Us</h1>
                        <p className="mb-8 max-w-md text-gray-600">
                            We are available for questions, feedback, or collaboration opportunities. Let us know how we can help!
                        </p>
                        <div>
                            <h2 className="mb-2 text-lg font-semibold">Contact Details</h2>
                            <ul className="space-y-2 text-gray-700">
                                <li>
                                    <strong>Phone:</strong> (123) 34567890
                                </li>
                                <li>
                                    <strong>Email:</strong>{' '}
                                    <a href="mailto:email@example.com" className="text-blue-600 hover:underline">
                                        email@example.com
                                    </a>
                                </li>
                                <li>
                                    <strong>Web:</strong>{' '}
                                    <a href="https://shadcnblocks.com" className="text-blue-600 hover:underline">
                                        shadcnblocks.com
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {/* Right form */}
                    <form className="space-y-4 rounded-lg border p-6 shadow-sm">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <Label htmlFor="first_name">First Name</Label>
                                <Input id="first_name" placeholder="First Name" />
                            </div>
                            <div>
                                <Label htmlFor="last_name">Last Name</Label>
                                <Input id="last_name" placeholder="Last Name" />
                            </div>
                        </div>
                        <div>
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" placeholder="Email" type="email" />
                        </div>
                        <div>
                            <Label htmlFor="subject">Subject</Label>
                            <Input id="subject" placeholder="Subject" />
                        </div>
                        <div>
                            <Label htmlFor="message">Message</Label>
                            <Textarea id="message" placeholder="Type your message here." />
                        </div>
                        <Button type="submit" className="w-full">
                            Send Message
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    );
}
