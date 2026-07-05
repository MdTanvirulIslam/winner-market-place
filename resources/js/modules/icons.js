import {
  ArrowDown,
  ArrowLeft,
  ArrowUp,
  BadgeCheck,
  CircleAlert,
  Download,
  Database,
  Bell,
  Boxes,
  CalendarDays,
  ChartColumnBig,
  ChartLine,
  ChartPie,
  Check,
  ChevronLeft,
  ChevronRight,
  ChevronsUpDown,
  Clock3,
  CreditCard,
  DollarSign,
  Eye,
  FileText,
  Globe,
  House,
  KeyRound,
  LayoutTemplate,
  Landmark,
  LifeBuoy,
  LockKeyhole,
  LogOut,
  Mail,
  Maximize,
  Menu,
  Minimize,
  MoreVertical,
  MessageSquareText,
  Moon,
  Palette,
  Paperclip,
  PhoneCall,
  Printer,
  ReceiptText,
  Search,
  SendHorizontal,
  Settings,
  Shield,
  ShoppingCart,
  SmilePlus,
  SlidersHorizontal,
  SquarePen,
  Star,
  Sun,
  Ticket,
  Trash2,
  TriangleAlert,
  Undo2,
  User,
  UserPlus,
  Users,
  Video,
  Wallet,
  X,
  createElement,
} from 'lucide';

const iconMap = {
  'arrow-down': ArrowDown,
  'arrow-left': ArrowLeft,
  'arrow-up': ArrowUp,
  'badge-check': BadgeCheck,
  bell: Bell,
  'circle-alert': CircleAlert,
  database: Database,
  boxes: Boxes,
  'calendar-days': CalendarDays,
  'chart-column-big': ChartColumnBig,
  'chart-line': ChartLine,
  'chart-pie': ChartPie,
  check: Check,
  'chevron-left': ChevronLeft,
  'chevron-right': ChevronRight,
  'chevrons-up-down': ChevronsUpDown,
  'clock-3': Clock3,
  'credit-card': CreditCard,
  'dollar-sign': DollarSign,
  download: Download,
  eye: Eye,
  'file-text': FileText,
  globe: Globe,
  house: House,
  'key-round': KeyRound,
  'layout-template': LayoutTemplate,
  landmark: Landmark,
  'life-buoy': LifeBuoy,
  'lock-keyhole': LockKeyhole,
  'log-out': LogOut,
  mail: Mail,
  maximize: Maximize,
  menu: Menu,
  'message-square-text': MessageSquareText,
  minimize: Minimize,
  'more-vertical': MoreVertical,
  moon: Moon,
  palette: Palette,
  paperclip: Paperclip,
  'phone-call': PhoneCall,
  printer: Printer,
  'receipt-text': ReceiptText,
  search: Search,
  'send-horizontal': SendHorizontal,
  settings: Settings,
  shield: Shield,
  'shopping-cart': ShoppingCart,
  'smile-plus': SmilePlus,
  'sliders-horizontal': SlidersHorizontal,
  'square-pen': SquarePen,
  star: Star,
  sun: Sun,
  ticket: Ticket,
  'trash-2': Trash2,
  'triangle-alert': TriangleAlert,
  'undo-2': Undo2,
  user: User,
  'user-plus': UserPlus,
  users: Users,
  video: Video,
  wallet: Wallet,
  x: X,
};

const renderSlot = (slot) => {
  const iconName = slot.dataset.icon;
  const iconNode = iconMap[iconName];

  if (!iconNode) {
    return;
  }

  const svg = createElement(iconNode, {
    'aria-hidden': 'true',
    focusable: 'false',
  });

  svg.classList.add('icon-svg');
  slot.replaceChildren(svg);
};

export const renderIcons = (root = document) => {
  root.querySelectorAll('[data-icon]').forEach((slot) => {
    renderSlot(slot);
  });
};

export const setIcon = (target, iconName) => {
  const slot = typeof target === 'string' ? document.querySelector(target) : target;

  if (!(slot instanceof Element)) {
    return;
  }

  slot.dataset.icon = iconName;
  renderSlot(slot);
};
