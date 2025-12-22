// API Response Types
export interface ApiResponse<T = any> {
  status?: string
  message?: string
  data?: T
  errors?: Record<string, string | string[]>
  [key: string]: any
}

export interface LoginResponse extends ApiResponse {
  status: 'success' | 'error'
  token?: string
  user_info?: {
    id: number
    name?: string
    buyer_name?: string
    email: string
  }
  admin_info?: {
    id: number
    username: string
    role: string
  }
}

export interface RegisterResponse extends ApiResponse {
  status: 'success' | 'error'
  token?: string
  user_info?: {
    id: number
    name: string
    email: string
  }
}

export interface PasswordResetResponse extends ApiResponse {
  status: 'success' | 'error'
  token?: string
}

export interface OrderResponse extends ApiResponse {
  id: number
  order_reference_number: string
  total_amount: number
  shipping_status: string
  buyer?: {
    id: number
    buyer_name: string
    email: string
  }
  selectedComponents?: Array<{
    id: number
    quantity: number
    hardware_component?: {
      id: number
      component_name: string
      brand: string
      retail_price: number
    }
  }>
}

export interface HardwareComponent {
  id: number
  component_name: string
  component_reference_number: string
  brand: string
  model: string
  specifications: string
  retail_price: number
  stock_quantity: number
  category_id?: number
  supplier_id?: number
  category?: {
    id: number
    category_name: string
  }
  supplier?: {
    id: number
    supplier_name: string
  }
}

export interface Category {
  id: number
  category_name: string
}

export interface Supplier {
  id: number
  supplier_name: string
}

export interface Buyer {
  id: number
  buyer_name: string
  email: string
  buyer_number?: string
  address?: string
}

export interface Admin {
  id: number
  username: string
  role: string
}

