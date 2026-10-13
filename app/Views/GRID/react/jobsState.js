/**
 * GRID Jobs Hub - Initial State & Persistence
 * Matches user's LinkedIn Jobs specification and divisions.
 */

const LOCAL_STORAGE_KEY = 'grid_jobs_state_v1';

export const defaultAppliedApplications = [
    {
        id: 'app-1',
        jobId: 'creative-hands-1',
        title: 'Freshers, Software Testing Opportunity, 2026 graduate Can also apply',
        company: 'Creative Hands HR',
        rating: 4.2,
        reviewCount: 331,
        location: 'Hyderabad',
        appliedDate: '17 Aug \'26',
        status: 'Resume viewed 4 weeks ago',
        statusType: 'resume_viewed',
        recruiterActive: 'Recruiter last active 3d ago',
        isQuickApply: true,
        source: 'grid_quick_apply',
        steps: [
            { id: 'step-1', label: 'Contacted By Email', date: '17 Aug \'26', completed: true },
            { id: 'step-2', label: 'Application Viewed', date: '17 Aug \'26', completed: true },
            { id: 'step-3', label: 'Resume Viewed', date: '17 Aug \'26', completed: false, isCurrent: true },
            { id: 'step-4', label: 'Awaiting Recruiter Action', date: null, completed: false, isCurrent: false }
        ],
        activity: {
            totalApplications: 13059,
            viewedByRecruiter: 13049
        },
        matchCriteria: [
            { label: 'Early Applicant', match: false },
            { label: 'Keyskills', match: false },
            { label: 'Location', match: true },
            { label: 'Work Experience', match: false },
            { label: 'Industry', match: false },
            { label: 'Department', match: true }
        ],
        recruiter: {
            name: 'HR Recruiter',
            role: 'HR at Creative Hands HR',
            activity: 'Recruiter last active 3d ago'
        }
    },
    {
        id: 'app-2',
        jobId: 'java-rd-1',
        title: 'Python Fullstack Developer - Part time / Contract',
        company: 'Java R&D',
        rating: 2.6,
        reviewCount: 157,
        location: 'Hyderabad (Hybrid)',
        appliedDate: '28 Jul \'26',
        status: 'Resume viewed 7 weeks ago',
        statusType: 'resume_viewed',
        recruiterActive: 'Recruiter last active 5d ago',
        isQuickApply: true,
        source: 'grid_quick_apply',
        logoText: 'J',
        logoBg: '#fee2e2',
        logoTextColor: '#ef4444',
        steps: [
            { id: 'step-1', label: 'Contacted By Email', date: '28 Jul \'26', completed: true },
            { id: 'step-2', label: 'Application Viewed', date: '30 Jul \'26', completed: true },
            { id: 'step-3', label: 'Resume Viewed', date: '02 Aug \'26', completed: true },
            { id: 'step-4', label: 'Awaiting Recruiter Action', date: null, completed: false, isCurrent: true }
        ],
        activity: {
            totalApplications: 4120,
            viewedByRecruiter: 3950
        },
        matchCriteria: [
            { label: 'Early Applicant', match: true },
            { label: 'Keyskills', match: true },
            { label: 'Location', match: true },
            { label: 'Work Experience', match: true },
            { label: 'Industry', match: true },
            { label: 'Department', match: true }
        ],
        recruiter: {
            name: 'Talent Acquisition Team',
            role: 'Recruiter at Java R&D',
            activity: 'Recruiter last active 5d ago'
        }
    },
    {
        id: 'app-3',
        jobId: 'sunknowledge-1',
        title: 'Human Resource Female Intern || Sunknowledge',
        company: 'Sunknowledge',
        rating: 3.4,
        reviewCount: 1462,
        location: 'Kolkata / Remote',
        appliedDate: '01 Sep \'26',
        status: 'Resume viewed 2 weeks ago',
        statusType: 'resume_viewed',
        recruiterActive: 'Recruiter last active 1w ago',
        isQuickApply: true,
        source: 'grid_quick_apply',
        steps: [
            { id: 'step-1', label: 'Contacted By Email', date: '01 Sep \'26', completed: true },
            { id: 'step-2', label: 'Application Viewed', date: '02 Sep \'26', completed: true },
            { id: 'step-3', label: 'Resume Viewed', date: '04 Sep \'26', completed: true },
            { id: 'step-4', label: 'Awaiting Recruiter Action', date: null, completed: false, isCurrent: true }
        ],
        activity: {
            totalApplications: 2840,
            viewedByRecruiter: 2800
        },
        matchCriteria: [
            { label: 'Early Applicant', match: true },
            { label: 'Keyskills', match: false },
            { label: 'Location', match: false },
            { label: 'Work Experience', match: true },
            { label: 'Industry', match: false },
            { label: 'Department', match: true }
        ],
        recruiter: {
            name: 'Pooja Sen',
            role: 'Lead HR at Sunknowledge',
            activity: 'Recruiter last active 1w ago'
        }
    },
    {
        id: 'app-4',
        jobId: 'floww-1',
        title: 'Solution Engineer',
        company: 'Floww.ai',
        rating: 4.4,
        reviewCount: 2,
        location: 'Bengaluru (Hybrid)',
        appliedDate: '15 Aug \'26',
        status: 'Resume viewed 4 weeks ago',
        statusType: 'resume_viewed',
        recruiterActive: 'Recruiter last active 6d ago',
        isQuickApply: true,
        source: 'grid_quick_apply',
        steps: [
            { id: 'step-1', label: 'Contacted By Email', date: '15 Aug \'26', completed: true },
            { id: 'step-2', label: 'Application Viewed', date: '16 Aug \'26', completed: true },
            { id: 'step-3', label: 'Resume Viewed', date: '18 Aug \'26', completed: true },
            { id: 'step-4', label: 'Awaiting Recruiter Action', date: null, completed: false, isCurrent: true }
        ],
        activity: {
            totalApplications: 890,
            viewedByRecruiter: 875
        },
        matchCriteria: [
            { label: 'Early Applicant', match: false },
            { label: 'Keyskills', match: true },
            { label: 'Location', match: true },
            { label: 'Work Experience', match: true },
            { label: 'Industry', match: true },
            { label: 'Department', match: true }
        ],
        recruiter: {
            name: 'Ananya Roy',
            role: 'Hiring Lead at Floww.ai',
            activity: 'Recruiter last active 6d ago'
        }
    },
    {
        id: 'app-5',
        jobId: 'waves-1',
        title: 'Business Development Executive',
        company: 'WAVES',
        rating: 3.2,
        reviewCount: 3,
        location: 'Hyderabad',
        appliedDate: '24 Aug \'26',
        status: 'Application sent 3 weeks ago',
        statusType: 'applied',
        recruiterActive: 'Recruiter last active 2w ago',
        isQuickApply: false,
        source: 'external',
        steps: [
            { id: 'step-1', label: 'Redirected to Company Career Portal', date: '24 Aug \'26', completed: true },
            { id: 'step-2', label: 'Application Submitted on External Site', date: '24 Aug \'26', completed: true }
        ],
        activity: {
            totalApplications: 1200,
            viewedByRecruiter: 450
        },
        matchCriteria: [
            { label: 'Early Applicant', match: true },
            { label: 'Keyskills', match: false },
            { label: 'Location', match: true },
            { label: 'Work Experience', match: false },
            { label: 'Industry', match: false },
            { label: 'Department', match: false }
        ],
        recruiter: {
            name: 'WAVES Careers Team',
            role: 'Recruitment at WAVES',
            activity: 'Recruiter last active 2w ago'
        }
    }
];

export function getInitialJobsState() {
    try {
        const saved = localStorage.getItem(LOCAL_STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (!parsed.appliedApplications || parsed.appliedApplications.length === 0 || !parsed.appliedApplications[0]?.steps) {
                parsed.appliedApplications = defaultAppliedApplications;
            }
            return parsed;
        }
    } catch (e) {
        console.warn('Could not load jobs state from localStorage', e);
    }

    return {
        userProfile: {
            name: 'K. Pavan Kumar',
            headline: 'Frontend Developer growing into Full Stack | AI & ML',
            location: 'Secunderabad, Telangana, India',
            experience: '1 yr exp',
            avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB0JMtxhQ02NsJAGucDr5mPhqMXM7S2dSh9ApxaOrjBpGFXL18mJrlzrzzEJRw737t1vu9Tcqri1rbH-CnDZtieK4L4BtmJzCP4_5FdnzbFXQUGfbfoh84rU333cFrjS78zpHLXqld0Zcyz5u-NytgOwsRE840swIFnrRHXrC1bG4E5sLOF6s5MgZLkhvd92Cpw1iZkw4GT5I-yyv57M7ve69ixFXJ2DvKQb3_TcB0iHyTUV4MCJ7Zs',
            banner: 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?auto=format&fit=crop&w=600&q=80',
            verified: true
        },
        preferences: {
            roles: ['Full-time', 'Web Developer', 'Python Developer', 'Full Stack Engineer', 'Software Engineer'],
            locationType: 'Hybrid or remote or on-site',
            location: 'Secunderabad'
        },
        trackerCounts: {
            saved: 4,
            applied: 62,
            updates: 13
        },
        savedJobIds: ['pref-1', 'more-4'],
        appliedJobIds: ['app-1', 'app-2', 'app-3', 'app-4', 'app-5'],
        dismissedJobIds: [],
        appliedApplications: defaultAppliedApplications,

        // Division 1: Jobs based on your preferences
        preferenceJobs: [
            {
                id: 'pref-1',
                title: 'Python Developer - Web Application Development',
                company: 'Apple',
                logoBg: '#000000',
                logoText: '',
                logoIcon: 'apple',
                location: 'Hyderabad, Telangana',
                workplaceType: 'On-site',
                alumniInfo: '1 school alumnus works here',
                postedTime: '1 week ago',
                isEasyApply: false,
                isPromoted: false,
                verifiedCompany: true,
                salary: null,
                skills: ['Python', 'Django', 'FastAPI', 'REST APIs', 'PostgreSQL'],
                description: 'Apple is seeking an exceptional Python Web Application Developer to join our Cloud Services engineering team in Hyderabad. You will design and deploy scalable internal tools and customer-facing application services.'
            },
            {
                id: 'pref-2',
                title: 'Python Full Stack developer',
                company: 'Infor HR Corp',
                logoBg: '#1e3a8a',
                logoText: 'IN',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                alumniInfo: 'Actively recruiting from your network',
                postedTime: '2 months ago',
                isEasyApply: false,
                isPromoted: false,
                verifiedCompany: false,
                salary: '₹6,00,000/yr - ₹10,00,000/yr',
                skills: ['Python', 'React.js', 'PostgreSQL', 'Docker', 'AWS'],
                description: 'Looking for a seasoned Full Stack Engineer proficient in Python backends and modern React frontends to scale enterprise HR applications.'
            },
            {
                id: 'pref-3',
                title: 'Software Engineer',
                company: 'Mirafra Technologies',
                logoBg: '#ea580c',
                logoText: 'MT',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                alumniInfo: '2 connections work here',
                postedTime: '1 week ago',
                isEasyApply: true,
                isPromoted: false,
                verifiedCompany: false,
                salary: '₹2,50,000/yr - ₹4,50,000/yr',
                skills: ['Core Java', 'Python', 'Data Structures', 'Linux'],
                description: 'Great opportunity for passionate software developers. Build mission-critical semiconductor design automation tools and distributed test architectures.'
            }
        ],

        // Division 2: Jobs that match your profile
        profileMatchJobs: [
            {
                id: 'prof-1',
                title: 'Hiring Internships',
                company: 'Vidyarishi',
                logoBg: '#0284c7',
                logoText: 'VR',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                alumniInfo: '3 company alumni work here',
                postedTime: '1 week ago',
                isEasyApply: true,
                isPromoted: false,
                salary: '₹15,00,000/mo - ₹25,00,000/mo',
                skills: ['JavaScript', 'HTML/CSS', 'Python Basics', 'Git'],
                description: 'Fast-track your career with high-impact software internships working directly alongside senior engineering mentors.'
            },
            {
                id: 'prof-2',
                title: 'Python AI Intern',
                company: 'Aethon AI',
                logoBg: '#4f46e5',
                logoText: 'AI',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                alumniInfo: 'Over 100 applicants in 24 hours',
                postedTime: '2 days ago',
                isEasyApply: true,
                isPromoted: true,
                salary: '₹20,000/mo stipend',
                skills: ['Python', 'PyTorch', 'HuggingFace', 'LangChain'],
                description: 'Hands-on Generative AI and LLM workflow implementation. Train, fine-tune, and integrate AI models into enterprise SaaS.'
            },
            {
                id: 'prof-3',
                title: 'Data Scientist I - Hyderabad (Hybrid) - Second Shift',
                company: 'Syneos Health',
                logoBg: '#f97316',
                logoText: 'SH',
                location: 'Gachibowli, Hyderabad',
                workplaceType: 'Hybrid',
                alumniInfo: '1 company alumnus works here',
                postedTime: '1 week ago',
                isEasyApply: false,
                isPromoted: false,
                salary: '₹8,00,000/yr - ₹12,00,000/yr',
                skills: ['R', 'Python', 'Clinical Data', 'Statistical Analysis'],
                description: 'Conduct exploratory data analysis and predictive modeling on healthcare datasets to accelerate life sciences breakthroughs.'
            },
            {
                id: 'prof-4',
                title: 'ML Engineer - Hyderabad (Hybrid)',
                company: 'Syneos Health',
                logoBg: '#f97316',
                logoText: 'SH',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                alumniInfo: '1 company alumnus works here',
                postedTime: '1 week ago',
                isEasyApply: false,
                isPromoted: false,
                salary: '₹9,00,000/yr - ₹14,00,000/yr',
                skills: ['MLOps', 'Python', 'TensorFlow', 'Docker', 'Kubernetes'],
                description: 'Deploy production-grade machine learning pipelines, model monitoring, and automated retraining systems.'
            }
        ],

        // Division 3: Recent job searches
        recentSearches: [
            {
                id: 'rs-1',
                query: 'DevOps Engineer',
                location: 'Hyderabad, Telangana, India',
                alertsCount: 40,
                status: 'Active'
            },
            {
                id: 'rs-2',
                query: 'Python Developer',
                location: 'India',
                alertsCount: 15,
                status: 'Active'
            },
            {
                id: 'rs-3',
                query: 'Java',
                location: 'India',
                alertsCount: 8,
                status: 'Active'
            }
        ],

        // Division 4: Show your interest in these companies
        interestedCompanies: [
            {
                id: 'comp-1',
                name: 'Amazon',
                size: '15,000+ employees • Greater Hyderabad Area',
                hiringInfo: '76 actively hiring',
                interested: false,
                logo: 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=128&q=80',
                banner: 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=400&q=80',
                initials: 'AMZ',
                themeColor: '#ff9900'
            },
            {
                id: 'comp-2',
                name: 'Bank of America',
                size: '10,000+ employees • Greater Hyderabad Area',
                hiringInfo: '32 actively hiring',
                interested: false,
                logo: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=128&q=80',
                banner: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=400&q=80',
                initials: 'BOA',
                themeColor: '#e11a22'
            },
            {
                id: 'comp-3',
                name: 'Persistent Systems',
                size: '15,000+ employees • Hyderabad & Pune',
                hiringInfo: '19 actively hiring',
                interested: false,
                logo: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=128&q=80',
                banner: 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=400&q=80',
                initials: 'PS',
                themeColor: '#f97316'
            },
            {
                id: 'comp-4',
                name: 'Qualcomm India',
                size: '8,000+ employees • Hyderabad (R&D)',
                hiringInfo: '45 actively hiring',
                interested: false,
                logo: 'https://images.unsplash.com/photo-1573164713988-8665fc963095?auto=format&fit=crop&w=128&q=80',
                banner: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=400&q=80',
                initials: 'QC',
                themeColor: '#0284c7'
            }
        ],

        // Division 6: More jobs for you (Comprehensive list)
        moreJobs: [
            {
                id: 'more-1',
                title: 'Junior AI Engineer',
                company: 'SendLayers Analytics',
                logoBg: '#38bdf8',
                logoText: 'SL',
                location: 'Hyderabad',
                workplaceType: 'On-site',
                postedTime: '1 hour ago',
                isEasyApply: true,
                isPromoted: false,
                salary: '₹4,50,000/yr - ₹7,00,000/yr',
                skills: ['Python', 'NLP', 'Vector DBs', 'FastAPI'],
                description: 'SendLayers Analytics is looking for an enthusiastic Junior AI Engineer to build automated document understanding and semantic search pipelines.'
            },
            {
                id: 'more-2',
                title: 'AI/ML Engineer - DevOps',
                company: 'Tech Mahindra',
                logoBg: '#ef4444',
                logoText: 'TM',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                postedTime: '2 hours ago',
                isEasyApply: false,
                isPromoted: false,
                salary: '₹7,00,000/yr - ₹11,00,000/yr',
                skills: ['Docker', 'Kubernetes', 'MLflow', 'Python', 'CI/CD'],
                description: 'Join Tech Mahindra MLOps practice to deploy, maintain, and monitor computer vision models in hybrid cloud Kubernetes clusters.'
            },
            {
                id: 'more-3',
                title: 'Associate Voice & Accent Insights',
                company: 'Amazon',
                logoBg: '#0f172a',
                logoText: 'a',
                location: 'Hyderabad On-site',
                workplaceType: 'On-site',
                postedTime: '1 day ago',
                isEasyApply: false,
                isPromoted: false,
                salary: '₹5,00,000/yr - ₹8,00,000/yr',
                skills: ['Speech Recognition', 'Audio Analysis', 'Linguistics', 'Python'],
                description: 'Analyze acoustic modeling accuracy and voice assistant interaction clarity for next-generation speech synthesis.'
            },
            {
                id: 'more-4',
                title: 'Data Scientist I',
                company: 'Qualcomm',
                logoBg: '#1e40af',
                logoText: 'Q',
                location: 'Hyderabad Hybrid',
                workplaceType: 'Hybrid',
                postedTime: '2 days ago',
                isEasyApply: true,
                isPromoted: true,
                salary: '₹12,00,000/yr - ₹18,00,000/yr',
                skills: ['Statistical Modeling', 'Machine Learning', 'Python', 'C++'],
                description: 'Optimize edge AI algorithms and on-device neural processing unit (NPU) model weights for next-generation Snapdragon platforms.'
            },
            {
                id: 'more-5',
                title: 'Engineer - Camera Systems (SW)',
                company: 'Qualcomm',
                logoBg: '#1e40af',
                logoText: 'Q',
                location: 'Greater Hyderabad Area',
                workplaceType: 'On-site',
                postedTime: '3 days ago',
                isEasyApply: false,
                isPromoted: true,
                salary: '₹14,00,000/yr - ₹22,00,000/yr',
                skills: ['Embedded C++', 'Image Processing', 'ISP Pipelines', 'Android HAL'],
                description: 'Develop low-level device drivers and camera hardware abstraction layer firmware for flagship mobile imaging sensors.'
            },
            {
                id: 'more-6',
                title: 'ML Engineer I',
                company: 'UST',
                logoBg: '#0284c7',
                logoText: 'UST',
                location: 'Hyderabad',
                workplaceType: 'Hybrid',
                postedTime: '3 days ago',
                isEasyApply: true,
                isPromoted: true,
                salary: '₹6,50,000/yr - ₹9,50,000/yr',
                skills: ['Scikit-learn', 'Pandas', 'REST APIs', 'Cloud ML'],
                description: 'Build predictive maintenance tools and forecasting algorithms for enterprise supply chain customers.'
            },
            {
                id: 'more-7',
                title: 'Scientific Computing Engineer - Drug Product Process Modeling',
                company: 'Novartis India',
                logoBg: '#c2410c',
                logoText: 'N',
                location: 'Greater Hyderabad Area',
                workplaceType: 'Hybrid',
                postedTime: '4 days ago',
                isEasyApply: false,
                isPromoted: true,
                salary: '₹15,00,000/yr - ₹24,00,000/yr',
                skills: ['Molecular Dynamics', 'Python', 'Computational Chemistry', 'HPC'],
                description: 'Apply high-performance computational simulations and fluid dynamics modeling to optimize pharmaceutical drug formulation workflows.'
            },
            {
                id: 'more-8',
                title: 'Associate AI/ML Engineer',
                company: 'Optum India',
                logoBg: '#ea580c',
                logoText: 'OP',
                location: 'Hyderabad',
                workplaceType: 'Hybrid',
                postedTime: '3 days ago',
                isEasyApply: true,
                isPromoted: false,
                salary: '₹7,50,000/yr - ₹10,50,000/yr',
                skills: ['Python', 'BERT', 'NLP', 'SQL', 'FastAPI'],
                description: 'Build healthcare clinical notes NLP information extraction systems using transformer architectures.'
            },
            {
                id: 'more-9',
                title: 'AI Engineer - Data and AI',
                company: 'Tiger Analytics',
                logoBg: '#0f766e',
                logoText: 'TA',
                location: 'Hyderabad',
                workplaceType: 'Hybrid',
                postedTime: '4 days ago',
                isEasyApply: false,
                isPromoted: false,
                salary: '₹8,00,000/yr - ₹13,00,000/yr',
                skills: ['Python', 'GenAI', 'Prompt Engineering', 'Azure OpenAI'],
                description: 'Design enterprise RAG (Retrieval-Augmented Generation) copilot applications for Fortune 500 financial and retail partners.'
            },
            {
                id: 'more-10',
                title: 'AI/ML Engineer',
                company: 'HyperThink Digital',
                logoBg: '#2563eb',
                logoText: 'HT',
                location: 'Greater Hyderabad Area',
                workplaceType: 'Hybrid',
                postedTime: '5 days ago',
                isEasyApply: true,
                isPromoted: false,
                salary: '₹6,00,000/yr - ₹9,00,000/yr',
                skills: ['Computer Vision', 'YOLO', 'OpenCV', 'PyTorch'],
                description: 'Implement real-time object detection and worker safety video analytics systems for industrial manufacturing sites.'
            },
            {
                id: 'more-11',
                title: 'AI/ML Engineer',
                company: 'Flyhigh',
                logoBg: '#7c3aed',
                logoText: 'FH',
                location: 'Hyderabad',
                workplaceType: 'Remote',
                postedTime: '1 week ago',
                isEasyApply: true,
                isPromoted: false,
                salary: '₹8,00,000/yr - ₹12,00,000/yr',
                skills: ['FastAPI', 'LLM Agents', 'LangGraph', 'Python'],
                description: 'Develop autonomous multi-agent AI systems for workflow automation and automated customer support triaging.'
            },
            {
                id: 'more-12',
                title: 'Associate Data Scientist',
                company: 'Infosys',
                logoBg: '#0284c7',
                logoText: 'INF',
                location: 'Hyderabad',
                workplaceType: 'Hybrid',
                postedTime: '1 week ago',
                isEasyApply: false,
                isPromoted: false,
                salary: '₹5,50,000/yr - ₹8,50,000/yr',
                skills: ['SQL', 'Tableau', 'Python', 'Machine Learning'],
                description: 'Analyze telemetry and customer churn patterns, creating dashboards and actionable data science models for global enterprise clients.'
            },
            {
                id: 'more-13',
                title: 'Machine Learning Software Engineer',
                company: 'Apple',
                logoBg: '#000000',
                logoText: '',
                logoIcon: 'apple',
                location: 'Hyderabad',
                workplaceType: 'On-site',
                postedTime: '1 week ago',
                isEasyApply: false,
                isPromoted: false,
                verifiedCompany: true,
                salary: '₹22,00,000/yr - ₹35,00,000/yr',
                skills: ['Core ML', 'Python', 'C++', 'Distributed Training'],
                description: 'Work on cutting-edge machine learning technology powering Apple intelligent assistant services across millions of devices worldwide.'
            },
            {
                id: 'more-14',
                title: 'Python + MLOps Engineer',
                company: 'Appzen',
                logoBg: '#6366f1',
                logoText: 'AZ',
                location: 'Hyderabad',
                workplaceType: 'Hybrid',
                postedTime: '2 weeks ago',
                isEasyApply: true,
                isPromoted: false,
                salary: '₹10,00,000/yr - ₹16,00,000/yr',
                skills: ['Python', 'Kubeflow', 'Airflow', 'AWS SageMaker'],
                description: 'Maintain end-to-end MLOps infrastructure supporting financial auditing and fraud prevention deep learning models.'
            }
        ]
    };
}

export const samplePythonJobDetail = {
    id: 'python-lead-detail-01',
    title: 'Senior Python Full Stack Developer',
    company: 'Novatech Global Solutions',
    rating: 4.4,
    reviewCount: 148,
    experience: '3 - 6 years',
    salary: '₹14,00,000 - 22,00,000 P.A.',
    location: 'Hyderabad (HITEC City / Madhapur) [Hybrid]',
    locationDetail: 'Plot 21, Knowledge City, HITEC City, Hyderabad, Telangana 500081',
    postedTime: '3 days ago',
    openings: 4,
    applicants: '92+',
    logoBg: '#3b82f6',
    logoText: 'NT',
    verifiedCompany: true,
    highlights: [
        '3+ years hands-on production experience in Python, Django / FastAPI, and modern frontend frameworks (React.js).',
        'Strong foundation in distributed microservices, PostgreSQL query optimization, Redis caching, and Docker containerized deployments.',
        'Proven track record designing RESTful & GraphQL APIs with high reliability and low latency.',
        'Experience working in an Agile/Scrum cross-functional team with automated CI/CD pipelines.'
    ],
    jobMatchScore: [
        { label: 'Early Applicant', status: 'match', desc: 'Applied within first 5 days' },
        { label: 'Key Skills', status: 'match', desc: 'Python, Django, React, PostgreSQL match your profile' },
        { label: 'Location', status: 'match', desc: 'Hyderabad Hybrid matches your preferred location' },
        { label: 'Work Experience', status: 'match', desc: '3+ years requirement fits your background' }
    ],
    description: {
        intro: 'Novatech Global Solutions is looking for a passionate Senior Python Full Stack Developer to build and scale next-generation enterprise SaaS cloud products. You will be responsible for creating robust microservice architectures, crafting high-performance user interfaces, and collaborating directly with product managers and architects.',
        responsibilities: [
            'Architect, build, and maintain scalable web backends and high-throughput microservices using Python (FastAPI/Django).',
            'Collaborate with UI/UX designers and frontend developers to deliver polished, performant React applications.',
            'Design optimized database schemas, relational models, indices, and queries using PostgreSQL and Redis.',
            'Build automated CI/CD pipelines, containerize applications with Docker, and deploy services on AWS (ECS, S3, RDS, Lambda).',
            'Participate in architecture discussions, conduct peer code reviews, mentor junior engineers, and uphold high code quality standards.'
        ],
        role: 'Full Stack Engineer / Backend Developer',
        industryType: 'Software Product / IT & SaaS',
        department: 'Engineering & Technology',
        employmentType: 'Full Time, Permanent',
        roleCategory: 'Software Development',
        education: {
            ug: 'B.Tech / B.E. in Computer Science, Information Technology, or equivalent practical experience',
            pg: 'MCA / M.Tech in Computer Science (Preferred, not mandatory)'
        },
        keySkills: [
            { name: 'Python', isKey: true },
            { name: 'FastAPI', isKey: true },
            { name: 'Django', isKey: true },
            { name: 'React.js', isKey: true },
            { name: 'PostgreSQL', isKey: false },
            { name: 'Docker', isKey: false },
            { name: 'Redis', isKey: false },
            { name: 'REST APIs', isKey: false },
            { name: 'AWS Cloud', isKey: false },
            { name: 'Microservices', isKey: false },
            { name: 'Git & CI/CD', isKey: false }
        ]
    },
    aboutCompany: {
        name: 'Novatech Global Solutions',
        overview: 'Novatech Global Solutions is a high-growth cloud intelligence platform delivering enterprise workflow automation and analytics to Fortune 500 organizations worldwide. Headquartered in San Francisco with major R&D centers in Hyderabad and Bengaluru, our team consists of over 850+ forward-thinking engineers and designers.',
        website: 'https://novatech-solutions.example',
        companySize: '500 - 1,000 employees'
    },
    imposterWarning: 'GRID and Novatech Global Solutions never ask candidates for security deposits, application charges, or money for interview scheduling. Always verify communications through official @novatech.com domains. If you encounter suspicious requests, please report them immediately.',
    similarJobs: [
        {
            id: 'sim-1',
            title: 'Python Developer - Web Application Development',
            company: 'Apple',
            logoBg: '#000000',
            logoText: '',
            logoIcon: 'apple',
            location: 'Hyderabad, Telangana',
            experience: '2 - 5 yrs',
            salary: '₹18,00,000 - 28,00,000 P.A.',
            skills: ['Python', 'Django', 'FastAPI', 'REST APIs'],
            postedTime: 'Posted 4 days ago'
        },
        {
            id: 'sim-2',
            title: 'Full Stack Python Engineer',
            company: 'Infor HR Corp',
            logoBg: '#1e3a8a',
            logoText: 'IN',
            location: 'Hyderabad (Hybrid)',
            experience: '3 - 6 yrs',
            salary: '₹12,00,000 - 18,00,000 P.A.',
            skills: ['Python', 'React.js', 'PostgreSQL', 'Docker'],
            postedTime: 'Posted 1 week ago'
        },
        {
            id: 'sim-3',
            title: 'Backend Software Engineer (Python)',
            company: 'Oracle',
            logoBg: '#c2410c',
            logoText: 'OR',
            location: 'Hyderabad (HITEC City)',
            experience: '4 - 7 yrs',
            salary: '₹16,00,000 - 26,00,000 P.A.',
            skills: ['Python', 'Kubernetes', 'Microservices', 'AWS'],
            postedTime: 'Posted 6 days ago'
        },
        {
            id: 'sim-4',
            title: 'Associate Python Developer',
            company: 'Mirafra Technologies',
            logoBg: '#ea580c',
            logoText: 'MT',
            location: 'Secunderabad',
            experience: '1 - 3 yrs',
            salary: '₹6,50,000 - 9,50,000 P.A.',
            skills: ['Python', 'SQL', 'Git', 'Linux'],
            postedTime: 'Posted 2 weeks ago'
        }
    ],
    sidebar: {
        interestedJobs: [
            {
                id: 'int-1',
                title: 'Senior Backend Python Architect',
                company: 'Regenorthosport Tech',
                rating: 4.1,
                reviews: 18,
                location: 'Hyderabad (Jubilee Hills)',
                posted: 'Posted 3 days ago',
                logoBg: '#8b5cf6',
                logoText: 'R'
            },
            {
                id: 'int-2',
                title: 'Python + Cloud Developer',
                company: 'Vecura Wellness Pvt Ltd',
                rating: 3.8,
                reviews: 95,
                location: 'Hyderabad, Bengaluru',
                posted: 'Posted 8 days ago',
                logoBg: '#0284c7',
                logoText: 'VW'
            },
            {
                id: 'int-3',
                title: 'Lead Python Engineer',
                company: 'Fortis HealthTech',
                rating: 4.3,
                reviews: 2140,
                location: 'Hyderabad',
                posted: 'Posted 10 days ago',
                logoBg: '#059669',
                logoText: 'FH'
            },
            {
                id: 'int-4',
                title: 'AI / Python SDE-2',
                company: 'Resolve Itech Corp',
                rating: 3.9,
                reviews: 32,
                location: 'Hyderabad, United Arab Emirates',
                posted: 'Posted 2 weeks ago',
                logoBg: '#ef4444',
                logoText: 'R'
            }
        ],
        reviews: {
            overallRating: 4.4,
            reviewCount: 148,
            featuredReview: {
                role: 'Senior Software Engineer in Hyderabad',
                likes: 'Exceptional engineering autonomy, modern Python / FastAPI stack, hybrid flexibility, and competitive compensation packages.',
                dislikes: 'Rapid sprint cycles and occasionally tight release deadlines during major quarter milestones.'
            }
        },
        salaryInsights: {
            role: 'Senior Python Developer',
            company: 'Novatech Global Solutions',
            range: '₹14.5 - ₹22.8 L/yr',
            avgMarketComparison: '18% higher than Hyderabad market average'
        },
        benefits: [
            { id: 'b1', title: 'Flexible Hybrid Work', desc: '3 days remote / 2 days office', icon: '💻' },
            { id: 'b2', title: 'Health & Wellness', desc: '₹10L coverage for family', icon: '🩺' },
            { id: 'b3', title: 'Learning Stipend', desc: '₹50,000/yr for AWS/Python certs', icon: '📚' },
            { id: 'b4', title: 'Meals & Transport', desc: 'Free meals & cab allowance', icon: '🍱' }
        ],
        servicesPromo: {
            title: 'Increase your profile visibility to recruiters up to 3 times',
            subtitle: 'Get a Featured Python Candidate badge and highlighted applications directly in hiring manager inboxes.',
            badge: 'GRID FastForward'
        }
    }
};

export function saveJobsState(state) {
    try {
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(state));
    } catch (e) {
        console.warn('Could not save jobs state to localStorage', e);
    }
}
