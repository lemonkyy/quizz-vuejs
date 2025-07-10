import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import RegisterForm from '../../components/form/RegisterForm.vue'

const register = vi.fn()

vi.mock('@/store/auth', () => ({
  useAuthStore: () => ({ register })
}))

beforeEach(() => {
  register.mockReset()
})

describe('RegisterForm.vue', () => {
  it('affiche une erreur si les champs sont vides', async () => {
    const wrapper = mount(RegisterForm, {
      global: {
        plugins: [createTestingPinia()]
      }
    })

    await wrapper.find('form').trigger('submit.prevent')
    expect(wrapper.html()).toContain('Veuillez remplir tous les champs.')
  })

  it('affiche une erreur si le mot de passe est trop faible', async () => {
    const wrapper = mount(RegisterForm, {
      global: {
        plugins: [createTestingPinia()]
      }
    })

    await wrapper.find('input[placeholder="Adresse e-mail"]').setValue('test@test.com')
    await wrapper.find('input[placeholder="Pseudonyme"]').setValue('testuser')
    await wrapper.find('input[placeholder="Mot de passe"]').setValue('weakpass')
    await wrapper.find('input[placeholder="Confirmation du mot de passe"]').setValue('weakpass')
    await wrapper.find('form').trigger('submit.prevent')
    expect(wrapper.html()).toContain('Le mot de passe est trop faible')
  })

  it('appelle register si tout est valide', async () => {
    const wrapper = mount(RegisterForm, {
      global: {
        plugins: [createTestingPinia()]
      }
    })

    await wrapper.find('input[placeholder="Adresse e-mail"]').setValue('test@test.com')
    await wrapper.find('input[placeholder="Pseudonyme"]').setValue('testuser')
    await wrapper.find('input[placeholder="Mot de passe"]').setValue('StrongPass12!')
    await wrapper.find('input[placeholder="Confirmation du mot de passe"]').setValue('StrongPass12!')
    await wrapper.find('form').trigger('submit.prevent')
    expect(register).toHaveBeenCalledWith('test@test.com', 'StrongPass12!', 'testuser')
  })
})
