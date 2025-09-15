// resources/js/pages/frontend/contact.tsx
import { Navbar } from '@/components/Frontend/navbar';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';

interface Blog {
    title: string;
    excerpt: string;
    image: string;
    link: string;
}
export default function BlogsPage({ blogs }: { blogs: Blog[] }) {
    return (
        <div className="relative min-h-screen w-full bg-white">
            <Navbar />
            <section className="mx-auto max-w-6xl px-4 py-12 text-center">
                <span className="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-600">Learning Resources</span>
                <h1 className="mt-4 text-4xl font-bold">Master the Stack</h1>
                <p className="mt-2 text-gray-500">
                    Discover the best resources to learn Laravel, React, TypeScript, and Inertia.js - everything you need to excel with LaraBaseX.
                </p>

                <Link href="/blogs" className="mt-4 inline-flex items-center text-primary hover:underline">
                    Explore all posts <ArrowRight className="ml-2 h-4 w-4" />
                </Link>

                <div className="mt-10 grid gap-6 md:grid-cols-3">
                    {blogs.map((blog, index) => (
                        <Card key={index} className="overflow-hidden">
                            <CardHeader className="p-0">
                                <img src={blog.image} alt={blog.title} className="h-48 w-full object-cover" />
                            </CardHeader>
                            <CardContent className="p-4">
                                <h3 className="text-lg font-semibold">{blog.title}</h3>
                                <p className="mt-2 text-sm text-gray-500">{blog.excerpt}</p>
                            </CardContent>
                            <CardFooter className="p-4 pt-0">
                                <a
                                    href={blog.link}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex items-center text-primary hover:underline"
                                >
                                    Read more <ArrowRight className="ml-1 h-4 w-4" />
                                </a>
                            </CardFooter>
                        </Card>
                    ))}
                </div>
            </section>
        </div>
    );
}
