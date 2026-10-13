// ─────────────────────────────────────────────
// Core Data Types for Portal KLA
// ─────────────────────────────────────────────

export interface PaginatedData<T> {
    data: T[];
    links: PaginationLink[];
    meta: PaginationMeta;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    first_page_url: string | null;
    last_page_url: string | null;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
    links: PaginationLink[];
}

export interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string | null;
    featured_image: string | null;
    url?: string | null;
    category?: {
        name: string;
        slug: string;
        icon?: string;
    } | null;
    opd?: {
        name: string;
        slug: string;
        type?: string;
    } | null;
    author?: string | null;
    views?: number;
    status: 'draft' | 'published';
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Service {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
    contact_info: string | null;
    status: 'draft' | 'published';
    created_at: string;
    updated_at: string;
}

export interface Facility {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    address: string | null;
    image: string | null;
    is_child_friendly: boolean;
    phone: string | null;
    category: string | null;
    created_at: string;
    updated_at: string;
}

export interface Publication {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    file_url: string | null;
    classification: 'public' | 'internal' | 'confidential';
    status: 'draft' | 'published';
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Page {
    id?: number;
    title: string;
    slug?: string;
    excerpt: string | null;
    content: string | null;
    status?: 'draft' | 'published';
}

export interface Statistic {
    id: number;
    label: string;
    value: string;
    icon: string | null;
    order: number;
}

// Home page props
export interface HomeProps {
    latestNews: Post[];
    upcomingEvents: AgendaEvent[];
    stats: {
        services: number;
        facilities: number;
    };
}

export interface AgendaEvent {
    id?: number | string;
    title: string;
    description?: string;
    start_at?: string;
    end_at?: string;
    location?: string;
}

// Inertia page props
export interface SharedProps {
    flash?: {
        success?: string;
        error?: string;
    };
}
