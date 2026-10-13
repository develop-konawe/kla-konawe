import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AppLayout from '../../layouts/AppLayout';
import { type Post } from '../../types';

interface PostShowProps {
    post: Post;
}

export default function PostShow({ post }: PostShowProps) {
    const date = post.published_at ? new Date(post.published_at) : null;
    const formatted = date?.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });

    return (
        <AppLayout title={post.title}>
            {/* Header */}
            <div className="bg-gradient-to-r from-primary-600 to-indigo-700 pt-40 pb-20 text-white relative overflow-hidden">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                        <Link href="/berita" className="inline-flex items-center text-indigo-200 hover:text-white font-bold mb-6 transition">
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Berita
                        </Link>
                        {formatted && (
                            <div className="text-indigo-200 font-medium text-sm mb-3">{formatted}</div>
                        )}
                        <h1 className="text-3xl md:text-4xl lg:text-5xl font-black tracking-tight leading-tight">{post.title}</h1>
                        {post.excerpt && (
                            <p className="mt-4 text-indigo-100 text-lg font-medium max-w-3xl">{post.excerpt}</p>
                        )}
                    </motion.div>
                </div>
            </div>

            {/* Content */}
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                    className="bg-white rounded-3xl shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden"
                >
                    {post.featured_image && (
                        <div className="overflow-hidden">
                            <img
                                src={post.featured_image.startsWith('http') ? post.featured_image : `/storage/${post.featured_image}`}
                                alt={post.title}
                                className="w-full max-h-96 object-cover"
                            />
                        </div>
                    )}
                    <article
                        className="p-8 md:p-12 prose prose-lg max-w-none text-gray-700 leading-relaxed
                            prose-headings:font-black prose-headings:text-gray-900
                            prose-a:text-primary-600 prose-a:font-bold
                            prose-img:rounded-2xl prose-blockquote:border-primary-500"
                        dangerouslySetInnerHTML={{ __html: post.content ?? '' }}
                    />
                </motion.div>

                {/* Back link */}
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ delay: 0.5 }}
                    className="mt-8 text-center"
                >
                    <Link
                        href="/berita"
                        className="inline-flex items-center text-primary-600 font-bold hover:text-primary-800 bg-primary-50 px-6 py-3 rounded-full hover:bg-primary-100 transition"
                    >
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                        Lihat Semua Berita
                    </Link>
                </motion.div>
            </div>
        </AppLayout>
    );
}
